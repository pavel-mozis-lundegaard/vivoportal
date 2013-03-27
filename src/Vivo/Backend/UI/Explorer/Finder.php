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
     * @var ExplorerInterface
     */
    protected $explorer;

    /**
     * @var \Vivo\CMS\Model\Entity
     */
    protected $entity;

    /**
     * @var Translator
     */
    protected $translator;


    public function init()
    {
        $this->entity = $this->explorer->getEntity();
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
            $this->explorer->setEntityByRelPath($relPath);
        } catch (EntityNotFoundException $e) {
            //TODO translate message

            $message = sprintf($this->translator->translate(
                    'Document with path `%s` does not exist.'), $relPath);
            $this->alert->addMessage($message, Alert::TYPE_ERROR);
        }
    }

    public function setExplorer(ExplorerInterface $explorer)
    {
        $this->explorer = $explorer;
        $this->explorer->getEventManager()->attach('setEntity', array ($this, 'onEntityChange'));
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
