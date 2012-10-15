<?php
namespace Vivo\Repo;

use Vivo\Mock\Document;

class cs extends Document {
	public $layout = 'Layouts/Page/SubPage'; 
	
	public function getContents() {
		return array(new \Vivo\CMS\Model\Content\Sample(),new \Vivo\CMS\Model\Content\Sample());
	}
}
