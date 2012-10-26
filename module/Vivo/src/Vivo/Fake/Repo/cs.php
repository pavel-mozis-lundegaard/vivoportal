<?php
namespace Vivo\Fake\Repo;

use Vivo\Fake\Document;

class cs extends Document {
	public $layout = 'Layouts/Page/SubPage';

	public function getContents() {
		return array(new \Vivo\CMS\Model\Content\Sample());
	}
}
