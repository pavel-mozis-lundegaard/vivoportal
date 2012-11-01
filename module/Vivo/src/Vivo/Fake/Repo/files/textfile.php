<?php
namespace Vivo\Fake\Repo\files;

use Vivo\Fake\Document;

class textfile extends Document {

    public function getContents() {

        $file = new \Vivo\CMS\Model\Content\File();
        $file->filename = 'test.txt';
        $file->mime_type = 'text/htmls';
        return array($file);
    }
}
