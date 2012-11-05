<?php
namespace Vivo\Fake\Repo;

use Vivo\Fake\Document;

class sk extends Document {
	public $layout = 'Layouts/Page/SubPage';

	public function getContents() {
		$file = new \Vivo\CMS\Model\Content\File();

		$file->mime_type = 'text/html';
	    return array($file);
	}
}
