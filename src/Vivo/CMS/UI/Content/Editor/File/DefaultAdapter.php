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
	 * Form textarea for WYSIWYG editor
	 * @var Vivo\UI\Form
	 */
	protected $form;

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
	    $this->cmsApi           = $cmsApi;
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
        $fileName = $this->content->getFilename();
        $inputStream  = $this->cmsApi->readResource($this->content, $fileName);

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
	    $this->view->showDownload = $this->showDownload;
		return parent::view();
	}

}
