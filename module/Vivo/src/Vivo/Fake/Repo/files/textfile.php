<?php
namespace Vivo\Fake\Repo\files;

use Vivo\Fake\Document;

class textfile extends Document {

    public function getContents() {
        return array(new \Vivo\CMS\Model\Content\File());
    }
}
