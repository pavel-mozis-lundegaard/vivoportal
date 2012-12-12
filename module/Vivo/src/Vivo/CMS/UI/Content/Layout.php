<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\ComponentFactory;

use Vivo\IO\InputStreamWrapper;
use Vivo\CMS\Api\CMS;
use Vivo\UI\ComponentInterface;
use Vivo\UI\ComponentContainer;
use Vivo\CMS\UI\Component;

/**
 * Layout UI component wraps the underlaying component to layout.
 */
class Layout extends Component
{
    const MAIN_COMPONENT_NAME = 'param';

    /**
     * @var CMS
     */
    protected $cms;

    public function __construct(CMS $cms)
    {
        $this->cms = $cms;
    }

    public function setMain(ComponentInterface $component)
    {
        $this->addComponent($component, self::MAIN_COMPONENT_NAME);
    }

    public function init()
    {
        parent::init();
        $this->prepareViewModel();
    }

    protected function prepareViewModel()
    {
        //TODO use child model to enable global template for layout component
        $is = $this->cms->readResource($this->content, 'Layout.phtml');
        $path = $this->document->getPath() . '/Layout.phtml';
        $filename = InputStreamWrapper::registerInputStream($is, $path);
        $this->view->setTemplate($filename);
    }

    public function getLayoutPanels()
    {
        return $this->content->getLayoutPanels();
    }
}
