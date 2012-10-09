<?php
namespace Vivo\Storage;

/**
 * @author miroslav.hajek
 */
class Dd implements StorageInterface {

	// mtime cache eliminuje opakovane volani contains/mtime na stejnou entitu.
	// v budoucnu muze mit platnost i delsi nez request (zeserializuje se do tmp) - toho muze byt vyuzivano
	// pro zmenseni loadu na databazi (vivo cluster?)
	private $mtime_cache = array();

	function __construct($db, $table) {
		$this->db = $db;
		$this->table = $table;
	}

	/**
	 * Checks whether item exists and (optionally) has not expired yet.
	 * Expiration parameter is used typically when virtual file system is used to implement a cache mechanism.
	 * @param string path to item
	 * @param expiration of item (in seconds); false means no expiration
	 * @return boolean TRUE if item exists and optionally if has not expired yet; otherwise FALSE
	 */
	function contains($path, $expiration = false) {
		$mtime = $this->mtime($path);
		return ($mtime && (!$expiration || ($mtime + $expiration > time())));
	}

	/**
	 * Returns item modification time in milliseconds.
	 * @param string path to item
	 * @return mixed item modification time in milliseconds or FALSE if item doesn't exist
	 */
	function mtime($path) {
		return
			$this->mtime_cache[$path] ?
				$this->mtime_cache[$path] :
				$this->mtime_cache[$path] = $this->db->getOne("SELECT mtime FROM {$this->table} WHERE path = ?", array($path));
	}

	/**
	 * Checks whether item on the given path is a file.
	 * @param string $path Path to the item
	 * @return bool
	 */
	function is_file($path) {
		return $this->db->getOne("SELECT 1 FROM {$this->table} WHERE path = ? AND type = 1", array($path)) ? true : false;
	}

	/**
	 * Prints an item to standard output (echo).
	 * If an item doesn't exist under specified path, nothing will be printed.
	 * @return void
	 */
	function output($path) {
		echo $this->db->getOne("SELECT data FROM {$this->table} WHERE path = ?", array($path));
	}


	/**
	 * Returns and from file system.
	 * If an item doesn't exist under specified path, NULL value will be returned.
	 * @param string path to item
	 * @param int item expiration (see the same argument of contains method to understand)
	 * @param boolean if TRUE, and object will be unserialized using standard PHP function unserialize. Value of this argument should be allways equivalent to the value passed to the so-called parameter of set method.
	 * @return mixed|null
	 */
	function get($path, $expiration = false, $unserialize = false) {
		if ($data = $this->db->getOne("SELECT data FROM {$this->table} WHERE path = ?".($expiration ? " AND mtime + $expiration > ?" : ""), array($path, $expiration, time()))) {
			return $unserialize ? unserialize($data) : $data;
		} else {
			return false;
		}
	}

	/**
	 * Saves item to file system.
	 * If and item under specified path already exists, it will be overwritten.
	 * @param string path to item
	 * @param mixed item data
	 * @param boolean if TRUE, item data will be serialized to file system using standard PHP function serialize (used typically for PHP objects)
	 * @return void
	 */
	function set($path, $variable, $serialize = false) {
		if ($serialize)
			$variable = serialize($variable);
		$names = explode('/', $path);
		$mtime = time();
		$type = 1;
		while (!empty($names)) {
			$path = implode('/', $names);
			$this->db->query(
				$this->contains($path) ?
					"UPDATE {$this->table} SET data = ?, mtime = ?, type = ? WHERE path = ?" :
					"INSERT INTO {$this->table} (data, mtime, type, path) VALUES (?, ?, ?, ?)", array($variable, $mtime, $type, $path));
			$this->mtime_cache[$path] = $mtime;
			$variable = null;
			$type = 2;
			array_pop($names);
		}
	}

	/**
	 * Touches item in file system.
	 * Touching means resetting an item modification time to the current system time. The function of this method is equivalent to standard UNIX command touch.
	 * @param path to item
	 */
	function touch($path) {
		$this->db->query("UPDATE {$this->table} SET mtime = ? WHERE path = ?", array(time(), $path));
	}

	/**
	 * Creates a symbolic link to item.
	 * The function of this method is equivalent to standard UNIX command ln (link).
	 * @param string path to target
	 * @param string path to item
	 * @return boolean
	 */
	function link($target, $link) {
		self::unsupported();
	}

	/**
	 * Renames/moves item.
	 * The function of this method is equivalent to standard UNIX command cp (copy).
	 * @param string path to item
	 * @param string target path
	 */
	function move($path, $target) {
		$this->remove($target);
		$this->db->query(
			"UPDATE {$this->table} SET path = CONCAT(?, SUBSTRING(path, ?)) WHERE path = ? OR path LIKE ?",
			array($target, mb_strlen($path) + 1, $path, "$path/%")
		);
		return true;
	}

	/**
	 * Copies item to another location.
	 * The function of this method is equivalent to standard UNIX command mv (move).
	 * @param string path to item
	 * @param string path to copy
	 */
	function copy($path, $target) {
		self::unsupported();
	}

	/**
	 * Returns list of child items.
	 * @param string path to item
	 * @return array List of paths denoting children items.
	 */
	function scan($path, $type = false) {
		return $this->db->getArray(
			"SELECT DISTINCT SUBSTRING_INDEX(SUBSTRING(path, ?), '/', 1) FROM {$this->table} WHERE path LIKE ? AND path NOT LIKE ?".
				(($type == self::FILE) ? " AND type = 1" : "").(($type == self::DIR) ? " AND type = 2" : ""),
			array(mb_strlen($path) + 2, "$path/%", "$path/%/%")
		);
	}

	/**
	 * Removes item from specified path. If the item doesn't exist, nothing happens.
	 * nedělá nic.
	 * @param path to item
	 * @return boolean TRUE on success, FALSE if item doesn't exist
	 */
	function remove($path) {
		$this->db->query("DELETE FROM {$this->table} WHERE path = ? OR path LIKE ?", array($path, "$path/%"));
		$this->mtime_cache = array();
	}

	/**
	 * Throws unsupported operation exception. Called when unsupported operation is called on object.
	 * @throws Vivo\UnsupportedException
	 */
	static function unsupported() {
		throw new \Vivo\UnsupportedException('Unsupported operation');
	}

	function __toString() {
		return get_class($this);
	}
}


