<?php
namespace Vivo\View\Renderer;

use Vivo\IO\InputStreamWrapper;

use Vivo\View\Resolver\UIResolver;
use Vivo\View\Exception;

use Zend\View\HelperPluginManager;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface;
use Zend\View\Variables;

/**
 * Renderer for rendering UI view models. Templates are included in it's context.
 * Object properties and local variables are double-underscored, to prevent naming collisions with included templates.
 */
class PhtmlRenderer implements Renderer
{

    /**
     * @var ModelInterface
     */
    private $__currentModel;

    public function getEngine()
    {
        return $this;
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     *
     * @param  ResolverInterface $resolver
     * @return RendererInterface
    */
    public function setResolver(ResolverInterface $resolver) {
        $this->resolver = $resolver;
    }
    /**
     * Returns currently rendered model.
     */
    public function getCurrentModel() {
        return $this->__currentModel;
    }

    /**
     * Sets currently rendered model.
     * @param ModelInterface $model
     */
    private function setCurrentModel(ModelInterface $model = null) {
        $this->__currentModel = $model;
    }

    public function render($model, $values = null)
    {
        if (!$model instanceof ModelInterface) {
            throw new Exception\InvalidArgumentException('Bad Argument');
        }
        if (!$model->getTemplate()) {
            throw new Exception\InvalidArgumentException(
                'Missing model template.');
        }

        $this->setCurrentModel($model);
        $values = $model->getVariables();

        if (null !== $values) {
            $this->setVars($values);
        }
        unset($values);

        // extract all assigned vars (pre-escaped), but not 'this'.
        $__vars = $this->vars()->getArrayCopy();
        if (array_key_exists('this', $__vars)) {
            unset($__vars['this']);
        }
        extract($__vars);
        unset($__vars); // remove $__vars from local scope

        $this->__file = $this->resolver->resolve($model->getTemplate());

        try {
            ob_start();
            include $this->__file;
            $this->__content = ob_get_clean();
        } catch (\Exception $e) {
            // we don't want to send broken output
            ob_clean();
            throw $e;
        }

        $this->setCurrentModel(null);
        return $this->__content;
    }

    public function setVars($variables)
    {
        if (!is_array($variables) && !$variables instanceof ArrayAccess
            && !$variables instanceof Variables) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Expected array, ArrayAccess or Variables object; received "%s"',
                    (is_object($variables) ? get_class($variables)
                        : gettype($variables))));
        }

        // Enforce a Variables container
        if (!$variables instanceof Variables) {
            $variablesAsArray = array();
            foreach ($variables as $key => $value) {
                $variablesAsArray[$key] = $value;
            }
            $variables = new Variables($variablesAsArray);
        }

        $this->__vars = $variables;
        return $this;
    }

    public function vars($key = null)
    {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }

        if (null === $key) {
            return $this->__vars;
        }
        return $this->__vars[$key];
    }

    /**
     * Set helper plugin manager instance
     *
     * @param  string|HelperPluginManager $helpers
     * @return PhpRenderer
     * @throws Exception\InvalidArgumentException
     */
    public function setHelperPluginManager($helpers)
    {
        if (is_string($helpers)) {
            if (!class_exists($helpers)) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'Invalid helper helpers class provided (%s)',
                        $helpers
                ));
            }
            $helpers = new $helpers();
        }
        if (!$helpers instanceof HelperPluginManager) {
            throw new Exception\InvalidArgumentException(sprintf(
                    'Helper helpers must extend Zend\View\HelperPluginManager; got type "%s" instead',
                    (is_object($helpers) ? get_class($helpers) : gettype($helpers))
            ));
        }
        $helpers->setRenderer($this);
        $this->__helpers = $helpers;

        return $this;
    }

    /**
     * Get helper plugin manager instance
     *
     * @return HelperPluginManager
     */
    public function getHelperPluginManager()
    {
        if (null === $this->__helpers) {
            $this->setHelperPluginManager(new HelperPluginManager());
        }
        return $this->__helpers;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return AbstractHelper
     */
    public function plugin($name, array $options = null)
    {
        return $this->getHelperPluginManager()->get($name, $options);
    }

    /**
     * Overloading: proxy to helpers
     *
     * Proxies to the attached plugin manager to retrieve, return, and potentially
     * execute helpers.
     *
     * * If the helper does not define __invoke, it will be returned
     * * If the helper does define __invoke, it will be called as a functor
     *
     * @param  string $method
     * @param  array $argv
     * @return mixed
     */
    public function __call($method, $argv)
    {
        $helper = $this->plugin($method);
        if (is_callable($helper)) {
            return call_user_func_array($helper, $argv);
        }
        return $helper;
    }
}
