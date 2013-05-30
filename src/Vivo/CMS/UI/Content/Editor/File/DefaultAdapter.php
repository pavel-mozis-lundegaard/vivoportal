<?php
namespace Vivo\CMS\UI\Content\Editor\File;

use Vivo\CMS\Api;
use Vivo\Form\Form;
use Vivo\CMS\UI\Content\Editor\AbstractAdapter;
use Vivo\Repository\Exception\PathNotSetException;

/**
 * Editor Adapter for general files
 */
class DefaultAdapter extends AbstractAdapter
{
    /**
     * Shows download button
     * @var bool
     */
    protected $showDownload = false;

    /**
     * Constructs Adapter
     */
    public function __construct(Api\CMS $cmsApi)
    {
        $this->cmsApi = $cmsApi;
    }

    /**
     * Initializes Adapter
    */
    public function init()
    {
        parent::init();
        try {
            if($this->content->getFileName()) {
                $this->showDownload = true;
            }
        }
        catch (PathNotSetException $e) {

        }
    }

    /**
     * Creates form
     */
    protected function doGetForm()
    {
        // NOT USED
        return new Form('download-resource'.$this->content->getUuid());
    }

    /**
     * Downloads file
     */
    public function downloadFile()
    {
        $mimeType = $this->content->getMimeType();
        $resource = 'resource.'.$this->content->getExt();
        $fileName = $this->content->getFilename();

        $inputStream  = $this->cmsApi->readResource($this->content, $resource);

        header('Content-type: '.$mimeType);
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        while(($b = $inputStream->read(4096)) !== false) {
            echo $b;
        }
        die();
    }

    /**
     * View Adapter
     */
    public function view()
    {
        $view = parent::view();
        $view->showDownload = $this->showDownload;

        return $view;
    }

}
