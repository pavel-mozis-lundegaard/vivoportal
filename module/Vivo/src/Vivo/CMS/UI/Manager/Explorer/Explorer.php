<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model;
use Vivo\CMS\UI\Manager\SiteSelector;
use Vivo\UI\ComponentContainer;

use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Http\Request;
use Zend\Session;

/**
 * Explorer component.
 */
class Explorer extends ComponentContainer implements EventManagerAwareInterface,
        EntityManagerInterface
{
    /**
     * Entity beeing explored.
     * @var \Vivo\CMS\Model\Entity
     */
    protected $entity;

    /**
     * Current component name.
     * @var string
     */
    protected $currentName = 'browser';

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var SiteSelector
     */
    protected $siteSelector;

    protected $explorerComponentFactory;

    /**
     * Constructor.
     * @param Request $request
     * @param CMS $cms
     * @param Session\ManagerInterface $sessionManager
     * @param SiteSelector $siteSelector
     * @param ExplorerComponentFactory $explorerComponentFactory
     */
    public function __construct(Request $request, CMS $cms,
            Session\ManagerInterface $sessionManager,
            SiteSelector $siteSelector,
            ExplorerComponentFactory $explorerComponentFactory
            )
    {
        $this->request = $request;
        $this->cms = $cms;
        $this->session = new Session\Container(__CLASS__, $sessionManager);
        $this->siteSelector = $siteSelector;
        $this->explorerComponentFactory = $explorerComponentFactory;
        $this->loadState();
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::init()
     */
    public function init()
    {
        parent::init();
        $this->loadEntity();
        $this->setCurrent($this->currentName);

        //attach events
        $this->siteSelector->getEventManager()->attach('setSite', array($this, 'onSiteChange'));
        $this->ribbon->getEventManager()->attach('itemClick', array ($this, 'onRibbonClick'));
    }

    /**
     * Load state from session.
     */
    protected function loadState()
    {
        $this->entity = $this->session->entity;
        $this->currentName = $this->session->currentName ? : $this->currentName;
    }

    /**
     * Saves the current state
     */
    public function saveState()
    {
        $this->session->entity = $this->entity;
        $this->session->currentName = $this->currentName;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::done()
     */
    public function done() {
        $this->saveState();
        parent::done();
    }

    /**
     * Creates and attach component to explorer(brovser, viewer, editor).
     *
     * Uses ExplorerComponentFactory for lazy creation of components.
     * @param string $name
     */
    public function setCurrent($name)
    {
        $component = $this->explorerComponentFactory->create($name, false);
        if ($component) {
            if ($this->hasComponent($name)) {
//                $this->removeComponent($name);
            }
            $this->currentName = $name;
            $this->addComponent($component, $name);
            $component->init();
        }
    }

    /**
     * Loads entity from url
     */
    protected function loadEntity()
    {
        if ($site = $this->getSite()) {
            if ($relPath = $this->request->getQuery('url', false)) {
                $entity = $this->cms->getSiteEntity($relPath, $site);
                $this->setEntity($entity);
            } elseif ($this->entity === null) {
                $entity = $this->cms->getSiteEntity('/', $site);
                $this->setEntity($entity);
            }
        }
    }

    /**
     * Callback for site change event.
     *
     * When site is changed, load root document.
     * @param Event $event
     */
    public function onSiteChange(Event $event)
    {
        $this->setEntityByRelPath('/');
    }

    /**
     * Callback for ribbon click event.
     * @param Event $event
     */
    public function onRibbonClick(Event $event)
    {
        $this->setCurrent($event->getParam('itemName'));
    }

    /**
     * Returns entity beeing explored.
     * @return \Vivo\CMS\Model\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param \Vivo\CMS\Model\Entity $entity
     */
    public function setEntity(Model\Entity $entity)
    {
        $this->eventManager->trigger(__FUNCTION__, $this, array('entity'=>$entity));
        $this->entity = $entity;
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\CMS\UI\Manager\Explorer\EntityManagerInterface::setEntityByRelPath()
     */
    public function setEntityByRelPath($relPath)
    {
        $this->setEntity($this->cms->getSiteEntity($relPath, $this->getSite()));
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\ComponentContainer::view()
     */
    public function view()
    {
        $this->view->currentName = $this->currentName;
        return parent::view();
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers(__CLASS__);
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Returns site beeing explored.
     * @return \Vivo\CMS\Model\Site
     */
    public function getSite()
    {
        return $this->siteSelector->getSite();
    }
}
