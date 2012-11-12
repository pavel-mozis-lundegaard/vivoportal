<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;


class Sample extends Model\Content {

	const FRONT_COMPONENT = 'Vivo\CMS\UI\Content\Sample';
	
	public function getFrontComponentClass() {
		return self::FRONT_COMPONENT;
	}
}