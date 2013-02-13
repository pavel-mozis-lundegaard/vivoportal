<?php
namespace Vivo\Backend\UI;

use Vivo\CMS\Api\Manager\Manager;
use Vivo\CMS\Model\Site;
use Vivo\UI\Component;
use Vivo\UI\PersistableInterface;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Component for selecting site for editing.
 */
class SiteSelector extends Component implements EventManagerAwareInterface,
        PersistableInterface
{

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Vivo\CMS\Api\Manager\Manager
     */
    protected $manager;

    /**
     * @var \Vivo\CMS\Model\Site
     */
    protected $site;

    /**
     * Constructor.
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->sites = $this->manager->getManageableSites();
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\PersistableInterface::loadState()
     */
    public function loadState($state)
    {
        $this->site = isset($state['site']) ? $state['site']
                : reset($this->sites);
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\PersistableInterface::saveState()
     */
    public function saveState()
    {
        return array('site' => $this->site);
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
     * Sets currently edited site.
     * @param string $siteName
     * @throws \Exception
     */
    public function set($siteName)
    {
        if (!key_exists($siteName, $this->sites)) {
            throw new \Exception('Site is not accessible.');
        }
        $this->setSite($this->sites[$siteName]);
    }

    /**
     * @param string $site
     * @throws \Exception
     */
    public function setSite(Site $site)
    {
        $this->site = $site;
        $this->eventManager
                ->trigger(__FUNCTION__, $this, array('site' => $site));
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $this->view->selectedSite = $this->site;
        $this->view->availableSites = $this->sites;
        return parent::view();
    }

    /**
     * @return \Vivo\CMS\Model\Site
     */
    public function getSite()
    {
        return $this->site;
    }
}
