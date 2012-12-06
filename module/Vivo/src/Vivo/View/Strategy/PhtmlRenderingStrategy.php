<?php
namespace Vivo\View\Strategy;

use Zend\View\Resolver\ResolverInterface;

use Vivo\View\Model\UIViewModel;
use Vivo\View\Renderer\UIRenderer;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;

/**
 * Rendering strategy for render phtml templates.
 *
 */
class PhtmlRenderingStrategy implements ListenerAggregateInterface
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer, ResolverInterface $resolver)
    {
        $this->setRenderer($renderer);
        $this->resolver = $resolver;
    }

    /**
     * Attach listeners.
     *
     * @see Zend\EventManager.ListenerAggregateInterface::attach()
     */
    public function attach(EventManagerInterface $events, $priority = 20)
    {
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $priority);
        $this->listeners[] = $events->attach(ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        // TODO: Auto-generated method stub
    }

    /**
     * Returns renderer if the ViewEvent contains corresponding model (UIViewModel).
     *
     * @param ViewEvent $e
     * @return \Zend\View\Renderer\RendererInterface|NULL
     */
    public function selectRenderer(ViewEvent $e)
    {
        $model = $e->getModel();
        if ($model instanceof UIViewModel) {
            $filename = $this->resolver->resolve($model->getTemplate());
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext == 'phtml') {
                return $this->renderer;
            }
        }
        return null;
    }

    /**
     * Injects result of rendering to the response.
     *
     * @param ViewEvent $e
     * @todo inject as a stream instead of content
     */
    public function injectResponse(ViewEvent $e)
    {
        $renderer = $e->getRenderer();
        if ($renderer !== $this->renderer) {
            return;
        }
        $result = $e->getResult();
        $response = $e->getResponse();
        $response->setContent($result);
    }

    /**
     * @param RendererInterface $renderer
     */
    protected function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }
}
