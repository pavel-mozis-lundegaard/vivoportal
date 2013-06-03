<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\UI\Component;
use Vivo\CMS\Api\Content\Fileboard as FileboardApi;

/**
 * UI component for content fileboard.
 */
class Fileboard extends Component
{
    /**
     * @var \Vivo\CMS\Api\Content\Fileboard
     */
    private $fileboardApi;

    /**
     * @var array
     */
    private $files = array();

    /**
     * Constructor
     */
    public function __construct(FileboardApi $fileboardApi)
    {
        $this->fileboardApi = $fileboardApi;
    }

    public function init()
    {
        parent::init();

        $this->files = $this->fileboardApi->getMediaList($this->content);
    }

    /**
     * @param string $uuid
     */
    public function download($uuid)
    {
        $this->fileboardApi->downloadByUuid($uuid);
    }

    public function view()
    {
        $view = parent::view();
        $view->files = $this->files;

        return $view;
    }
}
