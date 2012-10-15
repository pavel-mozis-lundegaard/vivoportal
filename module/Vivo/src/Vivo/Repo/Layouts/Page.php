<?php
namespace Vivo\Repo\Layouts;

use Vivo\Mock\Document;

class Page extends Document {

	public function getContents() {
		return array (new \Vivo\CMS\Model\Content\Layout());
	}
	
}