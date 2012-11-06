<?php
namespace Vivo\View\Resolver;

use Vivo\View\Exception\TemplateNotFoundException;

use Vivo\IO\InputStreamInterface;

use Vivo\IO\FileInputStream;

use Vivo\View\Model\UIViewModel;

use Vivo\View\Stream\Template;

use Zend\Config\Config;
use Zend\Mvc\Application;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Resolver\ResolverInterface;

/**
 * Resolver determines wich template file should by used for rendering.
 *
 */
class TemplateResolver implements ResolverInterface
{

    /**
     * @var array
     */
    private $templateMap = array();

    public function __construct($config)
    {
        $this->configure($config);
    }

    public function configure($config = array())
    {
        if (isset($config['templateMap'])) {
            $this->templateMap = array_merge($this->templateMap,
                            $config['templateMap']);
        }
    }

    public function resolve($name, RendererInterface $renderer = null)
    {
        if (isset($this->templateMap[$name])) {
            return $this->templateMap[$name];
        } else {
            throw new TemplateNotFoundException(
                            sprintf("%s: Template for '%s'not found.",
                                            __METHOD__, $name));
        }
    }
}
