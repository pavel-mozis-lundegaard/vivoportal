<?php
namespace Vivo\Storage;

use Vivo;
use Vivo\Logger;

/**
 * @author tzajicek
 */
class Cache extends StorageInterface {

	const NOW = 90000000000; // pseudo time of modification of items in mem cache (far far in the future = always actual)

	static $MAX_MEM_STRLEN = 100000; // limit caching items of type string in memory to 100KB
	static $ALLOW_APC = true;

	private $mem_cache = array(); // mem cache (request scope)

	public $use_mem;
	public $use_apc;

	public $mem_hits = 0;
	public $apc_hits = 0;
	public $lfs_hits = 0;

	public function __construct($root, $use_mem = true, $use_apc = true) {
		parent::__construct($root);
		$this->use_mem = $use_mem;
		$this->use_apc = self::$ALLOW_APC && function_exists('apc_fetch') && $use_apc && (VIVO_MODE == Vivo::MODE_WEB);
	}

	public function mtime($path) {
		return isset($this->mem_cache[$path]) ? self::NOW : parent::mtime($path);
	}

	public function contains($path, $expiration = false) {
		return isset($this->mem_cache[$path]) ? true : parent::contains($path, $expiration);
	}

	public function get($path, $expiration = false, $unserialize = false) {
		$variable = null;
		if (isset($this->use_mem) && (isset($this->mem_cache[$path]))) {
			$variable = $this->mem_cache[$path];
			$source = 'mem';
			$this->mem_hits++;
		} elseif ($this->use_apc && ($variable = apc_fetch("vivo-cache:$path"))) {
			$source = 'apc';
			$this->apc_hits++;
			$this->set_mem($path, $variable);
		} elseif ($variable = parent::get($path, $expiration, $unserialize)) {
			$source = 'lfs';
			$this->lfs_hits++;
			$this->set_mem($path, $variable);
			$this->set_apc($path, $variable);
		} else {
			$source = 'NOT_FOUND';
		}
		if (Vivo::$logger->level >= Logger::LEVEL_FINER)
			Vivo::$logger->finer("getting $path from cache:$source");
		return $variable;
	}

	private function set_mem($path, &$variable) {
		if ($this->use_mem && (!is_string($variable) || (strlen($variable <= self::$MAX_MEM_STRLEN))))
			$this->mem_cache[$path] = $variable;
	}

	private function set_apc($path, &$variable) {
		if ($this->use_apc && (!is_string($variable) || (strlen($variable <= self::$MAX_MEM_STRLEN))))
			apc_store("vivo-cache:$path", $variable);
	}

	public function set($path, $variable, $serialize = false) {
		$this->set_mem($path, $variable);
		$this->set_apc($path, $variable);
		return parent::set($path, $variable, $serialize);
	}

	public function remove($path) {
		if ($this->use_mem)
			unset($this->mem_cache[$path]);
		if ($this->use_apc)
			apc_delete("vivo-cache:$path");
		return parent::remove($path);
	}

}

