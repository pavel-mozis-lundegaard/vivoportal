<?php
namespace Vivo\Fake\Repo\Layouts\Page;

use Vivo\CMS\Model\Content\Layout;

use Vivo\Fake\Document;

class SubPage extends Document {

    protected $path = 'Layouts/Page/SubPage';

	public function getContents() {
		return array (new Layout());
	}
}