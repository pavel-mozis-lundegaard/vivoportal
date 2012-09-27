<?php
namespace Vivo\Repository\Storage;

// use Vivo\Util\FS;
// use Vivo\CMS\StorageInterface;

/**
 * Implementation of the virtual file system over local filesystem.
 */
class Local implements StorageInterface {

	/**
	 * @var string $root
	 */
	public $root;

	/**
	 * @param string $root Root path.
	 */
	public function __construct($root) {
		$this->root = $root;
	}

// 	function getAbsolutePath($path) {
// 		return $this->root.$path;
// 	}

	function dirname($path) {
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
	function mtime($path) {
		$abs_path = $this->getAbsolutePath($path);
		return file_exists($abs_path) ? filemtime($abs_path) : false;
	}

	function is_file($path) {
		return \is_file($this->getAbsolutePath($path));
	}

	/**
	 * Gets file size if exists.
	 * @param string $path Path to the file.
	 * @return int|false
	 */
	function size($path) {
		$abs_path = $this->getAbsolutePath($path);
		return file_exists($abs_path) ? filesize($abs_path) : false;
	}

	/**
	 * Outputs a file.
	 * @param string $path path to file being read.
	 */
	function output($path) {
		@readfile($this->getAbsolutePath($path));
	}

	/**
	 * Reads entire file into a string.
	 * @param string $path
	 * @param int $expiration Expiration time.
	 * @param bool $unserialize Will be unserialized after reading from a file.
	 * @return mixed
	 */
	function get($path, $expiration = false, $unserialize = false) {
		if ($this->contains($path, $expiration)) {
			$abs_path = $this->getAbsolutePath($path);
			//echo "get $abs_path<br>";
			$str = @file_get_contents($abs_path);
			if ($str !== false) {
				$variable = $unserialize ? unserialize($str) : $str;
				return $variable;
			}
		}
		return false;
	}

	/**
	 * Creates dir recursive.
	 * @param string $dir
	 * @throws Vivo\IOException Cannot create directory.
	 */
	function pdir($path) {
		$abs_path = $this->getAbsolutePath($path);
		$abs_dir_path = $this->dirname($abs_path);
		//echo "pdir $abs_dir_path<br>";
		if (!file_exists($abs_dir_path)) {
			//echo "mkdir $abs_dir_path<br>";
			if (@!mkdir($abs_dir_path, 0777, true)) {
				throw new \Vivo\IOException("Cannot create directory $abs_dir_path for $path");
			}
		}
	}

	/**
	 * Write a string to a file.
	 * @param string $path
	 * @param mixed $variable
	 * @param bool $serialize Will be serialized before writing to a file.
	 * @throws Vivo\IOException Cannot create directory.
	 * @return bool
	 */
	function set($path, $variable, $serialize = false) {
		$this->pdir($path);
		$abs_path = $this->getAbsolutePath($path);
		//echo "set $abs_path<br>";
		if ($serialize) {
			$serialized = serialize($variable);
			$result = file_put_contents($abs_path, $serialized);
		} else {
			$result = file_put_contents($abs_path, $variable);
		}
		if ($result === false)
			throw new \Vivo\IOException("Cannot write data to $abs_path for $path");
		chmod($abs_path, 0777);
		return true;
	}

	/**
	 * Sets access and modification time of file.
	 * @param string $path The name of the file being touched.
	 */
	function touch($path) {
		touch($this->getAbsolutePath($path));
	}

	/**
	 * Creates symbolic link.
	 * @param string $target Source path.
	 * @param string $link Destination path.
	 * @throws \Vivo\IOException Cannot create symlink.
	 */
	function link($target, $link) {
		$this->pdir($link);
		$this->pdir($target);
		$abs_link = $this->getAbsolutePath($link);
		$abs_target = $this->getAbsolutePath($target);

		$link_parts = explode('/', $abs_link);
		$target_parts = explode('/', $abs_target);
		for ($i = 0; $target_parts[$i] == $link_parts[$i] && $i < count($target_parts) && $i < count($link_parts); $i++);
		$rel_target = '';
		for ($j = 0; $j < count($link_parts) - $i - 1; $j++)
			$rel_target .= ($j ? '/' : '').'..';
		for ($j = $i; $j < count($target_parts); $j++)
			$rel_target .= '/'.$target_parts[$j];
		if (is_link($abs_link))
			unlink($abs_link);

		if (file_exists($abs_link))
			@unlink($abs_link);
		$dir = getcwd();
		chdir($this->dirname($abs_link)); // kvuli php safe modu
		if (!@symlink($rel_target, $abs_link))
			throw new \Vivo\IOException("Cannot create symlink $abs_link to $rel_target");
		chdir($dir);
	}

	/**
	 * Move.
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 * @return bool
	 */
	function move($path, $target) {
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
	function copy($path, $target) {
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
	function scan($path,  $type = false) {
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
	function remove($path) {
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

	function __toString() {
		return get_class($this).':'.$this->root;
	}

}


