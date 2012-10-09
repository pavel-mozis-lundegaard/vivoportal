<?php
namespace Vivo\Storage;

/**
 * Implementation of the virtual file system over local filesystem.
 */
class Local implements StorageInterface {

	const ENTITY_FILENAME = 'Entity.object';

	/**
	 * Root path.
	 * @var string $root
	 */
	private $root;

	/**
	 * @todo: Vivo\Util\SerializerInterface::serialize / unserialize
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $serializer;

	/**
	 * @param string $root Root path.
	 */
	public function __construct($root) {
		$this->root = $root;

		//@todo DI
		$this->setSerializer(new \Vivo\Serializer\Adapter\Entity());
	}

	private function getAbsolutePath($path) {
		return $this->root.$path;
	}

	private function getAbsoluteFilePath($path) {
		return $this->getAbsolutePath($path).DIRECTORY_SEPARATOR.self::ENTITY_FILENAME;
	}

	public function setSerializer(\Zend\Serializer\Adapter\AdapterInterface $serializer) {
		$this->serializer = $serializer;
	}

	private function serialize($object) {
		if($this->serializer) {
			return $this->serializer->serialize($object);
		}

		return /*serialize*/($object);
	}

	private function unserialize($object) {
		if($this->serializer) {
			return $this->serializer->unserialize($object);
		}

		return /*unserialize*/($object);
	}

	/**
	 * @todo prepsat telo na dirname($path) ???
	 *
	 * Enter description here ...
	 * @param unknown_type $path
	 * @return Ambigous <boolean, string>
	 */
	private function dirname($path) {
		return ($p = strrpos($path, '/')) ? substr($path, 0, $p) : false;
	}

	/**
	 * Checks whether a file or directory exists.
	 * @param string $path Path to the file.
	 * @param int $expiration
	 * @param bool
	 */
	function contains($path, $expiration = false) {
		$abs_path = $this->getAbsolutePath($path);
		return file_exists($abs_path) && ($expiration ? (filemtime($abs_path) + $expiration > time()) : true);
	}

	/**
	 * Gets file modification time if exists.
	 * @param string $path Path to the file.
	 * @return int|false
	 */
	public function mtime($path) {
		$abs_path = $this->getAbsolutePath($path);
		return file_exists($abs_path) ? filemtime($abs_path) : false;
	}

	public function isFile($path) {
		return is_file($this->getAbsolutePath($path));
	}

	/**
	 * Gets file size if exists.
	 * @param string $path Path to the file.
	 * @return int|false
	 *
	 * --- nikde se zde nepouziva, navenek by nemela byt videt, kdyz se tu nepouziva, tak smazat?
	 */
	private function size($path) {
		$abs_path = $this->getAbsolutePath($path);
		return file_exists($abs_path) ? filesize($abs_path) : false;
	}

	/**
	 * @todo: prepsat na stream, nebo jinak...
	 * Outputs a file.
	 * @param string $path path to file being read.
	 */
	public function output($path) {
		@readfile($this->getAbsoluteFilePath($path));
	}

	/**
	 * Creates dir recursive.
	 * @param string $dir
	 * @throws Vivo\IOException Cannot create directory.
	 */
	private function pdir($path) {
		$abs_path = $this->getAbsolutePath($path);
// 		$abs_dir_path = $this->dirname($abs_path);
// 		echo "pdir $abs_dir_path<br>";
		if (!file_exists($abs_path)) {
// 			echo "mkdir $abs_path<br>";
			if (@!mkdir($abs_path, 0777, true)) {
				throw new Exception\IOException("Cannot create directory $abs_path for $path");
			}
		}
	}

	/**
	 * Reads entire file into a string.
	 * @param string $path
	 * @param int $expiration Expiration time.
	 * @----param bool $unserialize Will be unserialized after reading from a file.
	 * @return sttring|null
	 */
	public function get($path, $expiration = false/*, $unserialize = false*/) {
		$return = null;
		if ($this->contains($path, $expiration)) {
			$abs_path = $this->getAbsoluteFilePath($path);
			// 			echo "get $abs_path<br>";
			$str = @file_get_contents($abs_path);
			if ($str !== false) {
				$return = $this->unserialize($str);
			}
		}
		return $return;
	}

	/**
	 * Write a string to a file.
	 * @param string $path
	 * @param mixed $variable
	 * @param bool $serialize Will be serialized before writing to a file.
	 * @throws Vivo\IOException Cannot create directory.
	 * ----@return bool Vyhazuje exception, tak k cemu return TRUE
	 */
	public function set($path, $variable/*, $serialize = false*/) {
// 		echo $path;
		$this->pdir($path);
		$abs_path = $this->getAbsoluteFilePath($path);
// 		echo "<br>set $abs_path<br>";
// 		if ($serialize) {
// 			$serialized = serialize($variable);
// 			$result = file_put_contents($abs_path, $serialized);
// 		} else {
// echo $abs_path;
			$result = file_put_contents($abs_path, $this->serialize($variable));
// 			echo "------------------\n";
// 		}
		if ($result === false) {
			throw new Exception\IOException("Cannot write data to $abs_path for $path");
		}
// 		chmod($abs_path, 0777); //@todo: tohle je tu proc ????? @see self::pdir
// 		return true;
	}

	/**
	 * Sets access and modification time of file.
	 * @param string $path The name of the file being touched.
	 */
	public function touch($path) {
		touch($this->getAbsoluteFilePath($path));
	}

	/**
	 * @deprecated zadne symbolicke linky vytvaret nebudeme
	 *
	 * Creates symbolic link.
	 * @param string $target Source path.
	 * @param string $link Destination path.
	 * @throws \Vivo\IOException Cannot create symlink.
	 */
// 	function link($target, $link) {
// 		$this->pdir($link);
// 		$this->pdir($target);
// 		$abs_link = $this->getAbsolutePath($link);
// 		$abs_target = $this->getAbsolutePath($target);

// 		$link_parts = explode('/', $abs_link);
// 		$target_parts = explode('/', $abs_target);
// 		for ($i = 0; $target_parts[$i] == $link_parts[$i] && $i < count($target_parts) && $i < count($link_parts); $i++);
// 		$rel_target = '';
// 		for ($j = 0; $j < count($link_parts) - $i - 1; $j++)
// 			$rel_target .= ($j ? '/' : '').'..';
// 		for ($j = $i; $j < count($target_parts); $j++)
// 			$rel_target .= '/'.$target_parts[$j];
// 		if (is_link($abs_link))
// 			unlink($abs_link);

// 		if (file_exists($abs_link))
// 			@unlink($abs_link);
// 		$dir = getcwd();
// 		chdir($this->dirname($abs_link)); // kvuli php safe modu
// 		if (!@symlink($rel_target, $abs_link))
// 			throw new \Vivo\IOException("Cannot create symlink $abs_link to $rel_target");
// 		chdir($dir);
// 	}

	/**
	 * Move.
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 * @return bool
	 */
	public function move($path, $target) {
		if (stripos(getenv('OS'), 'windows') !== false) {
			// solves problems with php rename function on Windows (Access denied sometimes)
			if ($this->copy($path, $target)) {
			    return (bool) $this->remove($path);
			} else {
				return false;
			}
		} else {
		    $this->pdir($target);
		    return rename($this->getAbsolutePath($path), $this->getAbsolutePath($target));
		}
	}

	/**
	 * Copy.
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 */
	public function copy($path, $target) {
		$count = 0;
		$this->pdir($target);
		if (is_dir($this->getAbsolutePath($path))) {
			@mkdir($this->getAbsolutePath($target));
			foreach ($this->scan($path) as $name)
				$count +=  $this->copy("$path/$name", "$target/$name");
		} else {
			$count += copy($this->getAbsolutePath($path), $this->getAbsolutePath($target));
		}
		return $count;
	}

	/**
	 * Returns array with file names.
	 * @param string $path
	 * @return array
	 */
	public function scan($path,  $type = false) {
		$names = array();
		$abs_path = $this->getAbsolutePath($path);
		if ($dir = @scandir($abs_path)) {
			foreach ($dir as $name) {
				if (($name{0} != '.') && (
						!$type || (
						(($type & self::FILE) && is_file("$abs_path/$name")) ||
						(($type & self::DIR) && is_dir("$abs_path/$name"))
					)
				))
					$names[] = $name;
			}
		}
		return $names;
	}

	/**
	 * Removes directory.
	 * @param string $path
	 * @return int Count of deleted directories.
	 */
	public function remove($path) {
		$count = 0;
		$abs_path = $this->getAbsolutePath($path);
		if (is_dir($abs_path)) {
			foreach ($this->scan($path) as $name)
				$count += $this->remove("$path/$name");
			rmdir($abs_path);
			$count++;
		} else if (is_file($abs_path) || is_link($abs_path)) {
			unlink($abs_path);
			$count++;
		}
		return $count;
	}

}
