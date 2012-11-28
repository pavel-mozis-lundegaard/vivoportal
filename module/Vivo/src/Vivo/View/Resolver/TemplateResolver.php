<?php
namespace Vivo\View\Resolver;

use Vivo\IO\InputStreamWrapper;
use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\Util\Path\PathParser;
use Vivo\View\Exception\TemplateNotFoundException;

use Zend\Config\Config;
use Zend\Mvc\Application;
use Zend\View\Renderer\RendererInterface;
use Zend\View\Resolver\ResolverInterface;

/**
 * Resolver determines which template file should be used for rendering.
 */
class TemplateResolver implements ResolverInterface
{
    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * Array of already resolved templates
     * @var array
     */
    protected $resolved = array();

    /**
     * @var array
     */
    private $templateMap = array();

    /**
     * @param ResourceManager $resourceManager
     * @param PathParser $parser
     * @param array $options
     */
    public function __construct(ResourceManager $resourceManager,
            PathParser $parser, $options = array())
    {
        $this->configure($options);
        $this->resourceManager = $resourceManager;
        $this->parser = $parser;
    }

    /**
     * Configures template resolver.
     * @param array $config
     */
    public function configure($config = array())
    {
        if (isset($config['template_map'])) {
            $this->templateMap = array_merge($this->templateMap,
                    $config['template_map']);
        }
    }

    /**
     * Method resolves the given name to the path, that could be included or opened.
     *
     * The path in this context also means url for registered stream wrappers.
     * If the name is existing path, the name is returned. Otherwise, method
     * searches the template map and find the path.
     *
     * @see \Zend\View\Resolver\ResolverInterface::resolve()
     */
    public function resolve($name, RendererInterface $renderer = null)
    {
        if (isset($this->resolved[$name])) {
            return $this->resolved[$name];
        }

        if (file_exists($name)) {
            $this->resolved[$name] = $name;
            return $name;
        }

        if (isset($this->templateMap[$name])) {
            $path = $this->templateMap[$name];
        } else {
            throw new TemplateNotFoundException(
                    sprintf("%s: Template for '%s' can't be resolved.",
                            __METHOD__, $name));
        }

        if (file_exists($path)) {
            $this->resolved[$name] = $path;
        } else {
            try {
                //load template from module
                $parts = $this->parser->parse($path);
                $is = $this->resourceManager
                        ->getResourceStream($parts['module'], $parts['path'],
                                'view');
                $path = InputStreamWrapper::registerInputStream($is, $path);
                $this->resolved[$name] = $path;
            } catch (\Exception $e) {
                if ($e instanceof \Vivo\Module\Exception\ExceptionInterface
                        || $e instanceof \Vivo\Util\Exception\CanNotParsePathException) {
                    throw new TemplateNotFoundException(
                            sprintf(
                                    "%s: Template '%s' ,that is defined in template map for '%s', not found.",
                                    __METHOD__, $path, $name), null, $e);

                } else {
                    //rethrow other exceptions
                    throw $e;
                }
            }
        }
        return $this->resolved[$name];
    }
}
