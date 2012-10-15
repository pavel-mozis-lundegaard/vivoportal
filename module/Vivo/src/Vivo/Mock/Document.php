<?php
namespace Vivo\Mock;

class Document extends \Vivo\CMS\Model\Document {
	
	public function __construct() {
//		parent::__construct();
		$this->name = "DOC";
	}
	
	public function getLayout() {
		return $this->layout;
	}
	
	public function getContents() {
		return array();
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function setPath($path) {
		$this->path = $path;
	}
	
}
