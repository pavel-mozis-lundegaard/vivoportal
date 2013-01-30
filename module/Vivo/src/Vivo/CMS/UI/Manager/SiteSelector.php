<?php
namespace Vivo\CMS\UI\Manager;

use Zend\Session\Container;

use Zend\Session\SessionManager;

use Vivo\CMS\Api\Manager\Manager;
use Vivo\CMS\Model\Site;
use Vivo\UI\Component;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 * Component for select site for editing.
 * @todo save site to session
 */
class SiteSelector extends Component implements EventManagerAwareInterface
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
     * @param Manager $manager
     */
    public function __construct(Manager $manager, SessionManager $sessionManager)
    {
        $this->manager = $manager;
        $this->session = new Container(__CLASS__, $sessionManager);
    }

    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        $this->eventManager->addIdentifiers(__CLASS__);
    }

    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Initializes component.
     */
    public function init()
    {
        $this->sites = $this->manager->getManageableSites();
        if ($siteName = $this->session->siteName) {
            $this->set($siteName);
        }

        $site = $this->getSite() ? : reset($this->sites);
        $this->setSite($site);
        parent::init();
    }

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
        $this->session->siteName = $site->getName();
        $this->eventManager->trigger(__FUNCTION__, $this, array ('site' => $this->site));
    }

    public function view()
    {
        $this->view->selectedSite = $this->site->getName();
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
