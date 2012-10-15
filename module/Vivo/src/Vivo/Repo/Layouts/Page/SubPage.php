<?php
namespace Vivo\Repo\Layouts\Page;

use Vivo\CMS\Model\Content\Layout;

use Vivo\Mock\Document;

class SubPage extends Document {
	
	public function getContents() {
		return array (new Layout());
	}
}