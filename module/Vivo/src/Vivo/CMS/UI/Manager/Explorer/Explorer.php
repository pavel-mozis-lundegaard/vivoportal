<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\UI\Manager\SiteSelector;

use Zend\Session\Container;

use Zend\Session\SessionManager;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model;
use Vivo\UI\ComponentContainer;

use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Http\Request;

/**
 *
 * @todo save entity/site to session
 */
class Explorer extends ComponentContainer implements EventManagerAwareInterface,
        EntityManagerInterface
{
    /**
     * Entity beeing explored.
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;

    /**
     *  Site beeing explored
     * @var \Vivo\CMS\Model\Site
     */
    private $site;

    /**
     * Current component
     * @var string
     */
    private $currentName = 'browser';

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    private $siteSelector;

    private $serviceManager;

    public function __construct(Request $request, CMS $cms, SessionManager $sessionManager, SiteSelector $siteSelector)
    {
        $this->request = $request;
        $this->cms = $cms;
        $this->session = new Container(__CLASS__, $sessionManager);
        $this->siteSelector = $siteSelector;
    }

    public function setServiceManager($manager)
    {
        $this->serviceManager = $manager;
    }

    public function loadState()
    {
        $this->entity = $this->session->entity;
        $this->currentName = $this->session->currentName;
    }

    public function init()
    {
        $this->loadState();
        $this->setCurrent($this->currentName);
        $this->siteSelector->getEventManager()->attach('setSite', array($this, 'onSiteChange'));
        $this->ribbon->getEventManager()->attach('itemClick', array ($this, 'onRibbonClick'));
        parent::init();
    }

    public function done() {
        $this->session->entity = $this->entity;
        $this->session->currentName = $this->currentName;
        parent::done();
    }

    /**
     * @todo move to factory class
     */
    public function createComponent($name)
    {
        switch($name) {
            case 'browser':
                $component = new Browser($this);
                break;
            case 'viewer':
                $component = new Viewer($this);
                break;
            case 'editor':
                $component = $this->serviceManager->get('Vivo\CMS\UI\Manager\Explorer\Editor');
                break;
            default:
                $component = null;
        }
        return $component;
    }

    public function setCurrent($name)
    {
        $component = $this->createComponent($name);
        if ($component) {
            $this->currentName = $name;

            if ($this->hasComponent('current')) {
//                $this->removeComponent('current');
            }
            $this->addComponent($component, 'current');
        }
    }

    public function loadEntity()
    {
        $site = $this->siteSelector->getSite();
        if ($this->site) {
            if ($relPath = $this->request->getQuery('url', false)) {
                $entity = $this->cms->getSiteEntity($relPath, $site);
                $this->setEntity($entity);
            } elseif ($this->entity === null) {
                $entity = $this->cms->getSiteEntity('', $site);
                $this->setEntity($entity);
            }
        }
    }

    public function onSiteChange(Event $event)
    {
        $this->site = $event->getParam('site');
        $this->setEntityByRelPath('/');
    }

    public function onRibbonClick(Event $event)
    {
        $this->setCurrent($event->getParam('itemName'));
    }

    /**
     * This method handles selection of any ribbon item
     * @param Vivo\RibbonItem $item
     */
    function invoke($item)
    {
        if ($item->name) {
            $this->current = $this->{$item->name};
            if ($item->name == 'security')
                $this->current->selected();
            // fix #21928
            if ($item->name == 'creator')
                $this->current->create();
            if ($this->last_item)
                $this->last_item->setActive(false);
            $this->last_item = $item;
            $item->setActive(true);
        }
    }

    /**
     * @return \Vivo\CMS\Model\Entity
     */
    public function getEntity()
    {
        if ($this->entity === null) {
            $this->loadEntity();
        }
        return $this->entity;
    }

    /**
     * @param Vivo\CMS\Model\Entity $entity
     */
    public function setEntity(\Vivo\CMS\Model\Entity $entity)
    {
        $this->eventManager->trigger(__FUNCTION__, $this, array('entity'=>$entity));
    }

    //     function setItem($name)
    //     {
    //         if ($name == 'viewer') {
    //             if ($this->entity instanceof Model\Document) {
    //                 $this->ribbon->select('tab1');
    //                 $this->invoke($this->ribbon->tab1->group1->viewer);
    //             } else {
    //                 $name = 'browser';
    //             }
    //         }
    //         if ($name == 'editor') {
    //             $this->ribbon->select('tab1');
    //             $this->invoke($this->ribbon->tab1->group2->editor);
    //         }
    //         if ($name == 'browser') {
    //             $this->ribbon->select('tab1');
    //             $this->invoke($this->ribbon->tab1->group1->browser);
    //         }
    //     }

    public function setEntityByRelPath($relPath)
    {
        $this->cms->getSiteEntity($relPath, $this->site);
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers(__CLASS__);
        $eventManager->getSharedManager()
                ->attach('Vivo\CMS\UI\Manager\SiteSelector', 'setSite',
                        array($this, 'onSiteChange'));
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    public function getSite()
    {
        return $this->site;
    }
}
