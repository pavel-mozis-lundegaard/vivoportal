<?php
namespace Vivo\CMS\Model;

class Site extends Entity {
	
	/**
	 * Non persistent value, set at runtime (in FS repository it could be name of the site folder)
	 * @var string
	 */
	private $name;
	
	public function __construct() {
		
	}
	
	public function getName() {
		return $this->name;
	}
}