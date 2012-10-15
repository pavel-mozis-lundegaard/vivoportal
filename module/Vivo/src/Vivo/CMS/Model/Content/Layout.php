<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

class Layout extends Model\Content {
	//TODO
	const FRONT_COMPONENT = 'Vivo\CMS\UI\Content\Layout';
	
	public function getFrontComponentClass() {
		return self::FRONT_COMPONENT;
	}
}