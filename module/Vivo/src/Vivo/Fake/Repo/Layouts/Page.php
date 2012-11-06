<?php
namespace Vivo\Fake\Repo\Layouts;

use Vivo\Fake\Document;

class Page extends Document {

    protected $path = 'Layouts/Page';

	public function getContents() {
		return array (new \Vivo\CMS\Model\Content\Layout());
	}

}