<?php
namespace Vivo\UI;

use Vivo\UI\ComponentInterface;

use Zend\Http\Response;
use Zend\View\HelperPluginManager;
use Zend\View\Helper\Doctype;

/**
 *
 */
class Page extends ComponentContainer
{

    const MAIN_COMPONENT_NAME = 'main';

    /**
     * @var string
     */
    private $doctype = Doctype::HTML5;

    /**
     * @var string Page title.
     */
    public $title;

    /**
     * @var array
     * @todo
     */
    public $html_attributes = array();

    /**
     * @var array HTML metas.
     */
    public $metas = array();

    /**
     * @var array HTML Links.
     */
    public $links = array();

    /**
     * @var array HTML Links with conditions.
     * @example
     * <!--[if IE 6]><link rel="stylesheet" href="/Styles/ie6.css" type="text/css" media="screen, projection"/><![endif]-->
     * @todo
     */
    public $conditional_links = array();

    /**
     * @var array
     */
    public $scripts = array();

    /**
     * @var HelperPluginManager
     */
    protected $viewHelpers;

    /**
     * @param ComponentInterface $component
     * @param array $options
     */
    public function __construct(Response $response)
    {
        parent::__construct();
        $this->response = $response;
    }

    public function init() {
        $this->response->getHeaders()->addHeaderLine('Content-Type: text/html');
        parent::init();
    }

    /**
     * Sets main UI component of the page.
     * @param ComponentInterface $component
     */
    public function setMain(ComponentInterface $component)
    {
        $this->addComponent($component, self::MAIN_COMPONENT_NAME);
    }

    /**
     * Sets HTML doctype of page.
     *
     * Also accept names of constants defined in Zend\View\Helper\Doctype.
     * @param string $doctype
     * @see \Zend\View\Helper\Doctype
     */
    public function setDoctype($doctype)
    {
        if (defined('DocType::'.$doctype)) {
            $this->doctype = Doctype::$doctype;
        } else {
            $this->doctype = $doctype;
        }
    }

    /**
     * Sets head metas.
     * @param array $metas
     */
    public function setMetas(array $metas)
    {
        $this->metas = $metas;
    }

    /**
     * Sets head links.
     * @param array $links
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
    }

    /**
     * Sets head scripts.
     * @param array $scripts
     */
    public function setScripts(array $scripts)
    {
        $this->scripts = $scripts;
    }

    /**
     * Injects HelperPluginManager.
     * @param HelperPluginManager $viewHelpers
     */
    public function setViewHelpers(HelperPluginManager $viewHelpers)
    {
        $this->viewHelpers = $viewHelpers;
    }

    /**
     * Preonfigure view helpers.
     */
    protected function configureViewHelpers()
    {
        /* @var $headLink \Vivo\View\Helper\HeadLink */
        $headLink = $this->viewHelpers->get('headLink');
        foreach ($this->links as $link) {
            $headLink->append($headLink->createData($link));
        }

        /* @var $headScript \Vivo\View\Helper\HeadScript */
        $headScript = $this->viewHelpers->get('headScript');
        foreach ($this->scripts as $script) {
            $headScript->append($headScript->createData($script['type'], $script));
        }

        /* @var $headMeta \Zend\View\Helper\HeadMeta */
        $headMeta = $this->viewHelpers->get('headMeta');
        foreach ($this->metas as $meta) {
            if (isset($meta['name'])) {
                $headMeta->appendName($meta['name'], $meta['content']);
            } elseif (isset($meta['http-equiv'])){
                $headMeta->appendHttpEquiv($meta['http-equiv'], $meta['content']);
            } elseif (isset($meta['charset']) && $this->doctype == Doctype::HTML5) {
                $headMeta->setCharset($meta['charset']);
            }
        }
        /* @var $docType \Zend\View\Helper\DocType */
        $docType = $this->viewHelpers->get('docType');
        $docType->setDoctype($this->doctype);
    }

    public function view()
    {
        $this->configureViewHelpers();
        $this->view->setVariable('doctype', $this->doctype);
        $this->view->links = $this->links;
        $this->view->scripts = $this->scripts;
        return parent::view();
    }
}
