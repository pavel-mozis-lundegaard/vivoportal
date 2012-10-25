<?php
namespace Vivo\CMS\UI;

use Vivo\UI\ComponentInterface;

use Zend\Http\Response;
use \Zend\View\Helper\Doctype;

/**
 * @todo use Zend\View\Helper\Doctype;
 *
 */
class Page extends Component
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
     */
    public $conditional_links = array();
    /**
     * @var array
     */
    public $scripts = array(
        array(
            'type' => 'text/javascript',
            'src' => '/Resources/Scripts/vivo.js'));

    /**
     * @param ComponentInterface $component
     * @param array $options
     */
    public function __construct(Response $response, $options = null)
    {
        parent::__construct(null, null);
        $response->getHeaders()->addHeaderLine('Content-Type: text/html');
        if (isset($options['doctype'])) {
            $this->setDoctype($options['doctype']);
        }
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
     * @param unknown_type $doctype
     */
    public function setDoctype($doctype)
    {
        $this->doctype = $doctype;
    }

    public function view()
    {
        $this->view->setVariable('doctype', $this->doctype);
        return parent::view();
    }

    //TODO methods for modifying html head (css, js, keywords etc.)
    //TODO implement resource(js,css) merging
}
