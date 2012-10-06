<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 * 
 * @version $Id: Factory.php 1354 2011-05-23 07:45:52Z tzajicek $
 */
namespace Vivo\CMS\Workflow;

use Vivo\CMS;

/**
 * Workflow factory. 
 * @author tzajicek
 */
class Factory {
	/**
	 * @var array
	 */
	public static $workflows = array();
	
	/**
	 * Private constructor function to prevent external instantiation. 
	 */
	private function __construct() { }
	
	/** 
	 * @param string Workflow name
	 * @return Vivo\CMS\Workflow
	 */
	static function get($type) {
		return self::$workflows[$type];
	}
	
	/**
	 * @param Vivo\CMS\Workflow $workflow
	 */
	static function add($workflow) {
		self::$workflows[get_class($workflow)] = $workflow;
	}
	
}

