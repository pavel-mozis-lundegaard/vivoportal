<?php
namespace Vivo\View\Strategy;

use Vivo\View\Model\UIViewModel;
use Vivo\View\Renderer\UIRenderer;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;

/**
 * Rendering strategy for renderign view model of UI components(or component tree).
 *
 */
class UIRenderingStrategy implements ListenerAggregateInterface
{

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @param RendererInterface $renderer
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->setRenderer($renderer);
    }

    /**
     * Attach listeners.
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
     * Return renderer if the ViewEvent contains corresponding model (UIViewModel).
     * @param ViewEvent $e
     * @return \Zend\View\Renderer\RendererInterface|NULL
     */
    public function selectRenderer(ViewEvent $e)
    {
        if ($e->getModel() instanceof UIViewModel) {
            return $this->renderer;
        }
        return null;
    }

    /**
     * Inject result of rendering to the response.
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
