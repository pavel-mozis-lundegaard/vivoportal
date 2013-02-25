<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\Repository\Exception\EntityNotFoundException;
use Vivo\Service\Initializer\TranslatorAwareInterface;
use Vivo\UI\Alert;
use Vivo\UI\Component;
use Zend\EventManager\Event;
use Zend\I18n\Translator\Translator;

class Finder extends Component implements TranslatorAwareInterface
{

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Vivo\CMS\Model\Entity
     */
    protected $entity;
    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::init()
     */

    /**
     * @var Translator
     */
    protected $translator;


    public function init()
    {
        $this->entity = $this->entityManager->getEntity();
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $this->view->entity = $this->entity;
        return parent::view();
    }

    /**
     * @param string $relPath
     */
    public function set($relPath)
    {
        try {
            $this->entityManager->setEntityByRelPath($relPath);
        } catch (EntityNotFoundException $e) {
            //TODO translate message

            $message = sprintf($this->translator->translate(
                    'Document with path `%s` does not exist.'), $relPath);
            $this->alert->addMessage($message, Alert::TYPE_ERROR);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityManager->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));
    }

    /**
     * Callback for entity change event.
     * @param Event $e
     */
    public function onEntityChange(Event $e)
    {
        $this->entity = $e->getParam('entity');
    }

    /**
     * Return current entity.
     * @return \Vivo\CMS\Model\Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets Alert component.
     * @param Alert $alert
     */
    public function setAlert(Alert $alert)
    {
        $this->alert = $alert;
    }

    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }
}
