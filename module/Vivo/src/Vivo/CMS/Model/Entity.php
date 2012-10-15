<?php
namespace Vivo\CMS\Model;

class Entity {
	
	private $path;
	
	public function getPath() {
		return $this->path;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
}