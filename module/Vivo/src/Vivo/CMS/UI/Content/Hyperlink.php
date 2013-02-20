<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\UI;
use Vivo\Util\RedirectEvent;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * UI to redirects to the URL.
 */
class Hyperlink extends UI\Component implements EventManagerAwareInterface
{

    /**
     *
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Hyperlink initialization.
     */
    public function init()
    {
        $url = $this->content->getUrl();
        $this->events->trigger(new RedirectEvent($url));

        //$this->redirector->redirect($url);
//TODO add meta redirect
//        $this->parent('Vivo\UI\Page')->metas[] = array(
//                'http-equiv' => 'Refresh', 'content' => '0;url=' . $this->url);
    }

    public function getEventManager()
    {
        return $this->events;
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->events = $eventManager;
    }
}

