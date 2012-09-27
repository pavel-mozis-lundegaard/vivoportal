<?php
namespace Vivo\Repository\Storage;

interface StorageInterface {

	const FILE = 'FILE'; //@fixme: V interface konstanty?
	const DIR  = 'DIR';

	public function getIndexer();

	/**
	 * Checks whether item exists and (optionally) has not expired yet.
	 * Expiration parameter is used typically when virtual file system is used to implement a cache mechanism.
	 * @param string path to item
	 * @param expiration of item (in seconds); false means no expiration
	 * @return boolean TRUE if item exists and optionally if has not expired yet; otherwise FALSE
	 */
	public function contains($path, $expiration = false);

	/**
	 * Returns item modification time in milliseconds.
	 * @param string path to item
	 * @return mixed item modification time in milliseconds or FALSE if item doesn't exist
	 */
	public function mtime($path);

	/**
	 * Checks whether item on the given path is a file.
	 * @param string $path Path to the item
	 * @return bool
	 */
	public function isFile($path);

	/**
	 * Prints an item to standard output (echo).
	 * If an item doesn't exist under specified path, nothing will be printed.
	 * @return void
	 */
	public function output($path);

	/**
	 * Returns and from file system.
	 * If an item doesn't exist under specified path, NULL value will be returned.
	 * @param string path to item
	 * @param int item expiration (see the same argument of contains method to understand)
	 * @param boolean if TRUE, and object will be unserialized using standard PHP public function unserialize. Value of this argument should be allways equivalent to the value passed to the so-called parameter of set method.
	 * @return mixed|null
	 */
	public function get($path, $expiration = false, $unserialize = false);

	/**
	 * Saves item to file system.
	 * If and item under specified path already exists, it will be overwritten.
	 * @param string path to item
	 * @param mixed item data
	 * @param boolean if TRUE, item data will be serialized to file system using standard PHP public function serialize (used typically for PHP objects)
	 * @return void
	 */
	public function set($path, $variable, $serialize = false);

	/**
	 * Touches item in file system.
	 * Touching means resetting an item modification time to the current system time. The public function of this method is equivalent to standard UNIX command touch.
	 * @param path to item
	 */
	public function touch($path);

	/**
	 * Creates a symbolic link to item.
	 * The public function of this method is equivalent to standard UNIX command ln (link).
	 * @param string path to target
	 * @param string path to item
	 * @return boolean
	 */
	//public function link($target, $link);

	/**
	 * Renames/moves item.
	 * The public function of this method is equivalent to standard UNIX command cp (copy).
	 * @param string path to item
	 * @param string target path
	 */
	public function move($path, $target);

	/**
	 * Copies item to another location.
	 * The public function of this method is equivalent to standard UNIX command mv (move).
	 * @param string path to item
	 * @param string path to copy
	 */
	public function copy($path, $target);

	/**
	 * Returns list of child items.
	 * @param string path to item
	 * @return array List of paths denoting children items.
	 */
	public function scan($path, $type = false);

	/**
	 * Removes item from specified path. If the item doesn't exist, nothing happens.
	 * nedělá nic.
	 * @param path to item
	 * @return boolean TRUE on success, FALSE if item doesn't exist
	 */
	public function remove($path);
}
