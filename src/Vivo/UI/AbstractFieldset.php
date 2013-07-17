<?php
namespace Vivo\UI;

use Vivo\Service\Initializer\TranslatorAwareInterface;
use Vivo\Service\Initializer\InputFilterFactoryAwareInterface;

use Zend\I18n\Translator\Translator;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\Form\Fieldset as ZfFieldset;

/**
 * AbstractFieldset
 */
abstract class AbstractFieldset extends ComponentContainer implements TranslatorAwareInterface,
                                                                      ZfFieldsetProviderInterface
{
    /**
     * Translator instance
     * @var Translator
     */
    protected $translator;

    /**
     * Fieldset object
     * @var ZfFieldset
     */
    protected $fieldset;

    /**
     * Returns the Fieldset object
     * @return ZfFieldset
     */
    public function getFieldset()
    {
        if (!$this->fieldset) {
            $this->fieldset = $this->doGetFieldset();
        }
        return $this->fieldset;
    }

    /**
     * The actual creation of the Fieldset object
     * @return ZfFieldset
     */
    abstract protected function doGetFieldset();

    /**
     * Injects translator
     * @param \Zend\I18n\Translator\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator   = $translator;
    }

    /**
     * Returns ZF Fieldset
     * @return ZfFieldset
     */
    public function getZfFieldset()
    {
        return $this->getFieldset();
    }

    /**
     * Sets name for this ZfFieldset
     * Use to override the underlying fieldset name
     * @param string $zfFieldsetName
     */
    public function setZfFieldsetName($zfFieldsetName)
    {
        $fieldset   = $this->getFieldset();
        $fieldset->setName($zfFieldsetName);
    }

    /**
     * Returns name for this ZfFieldset
     * @return string
     */
    public function getZfFieldsetName()
    {
        $fieldset   = $this->getFieldset();
        return $fieldset->getName();
    }

    /**
     * View listener
     * Passes the fieldset to the view
     */
    public function viewListener()
    {
        $fieldset                   = $this->getFieldset();
        $this->getView()->fieldset  = $fieldset;
    }

    /**
     * Attaches listeners
     * @return void
     */
    public function attachListeners()
    {
        parent::attachListeners();
        $eventManager   = $this->getEventManager();
        //View
        $eventManager->attach(ComponentEventInterface::EVENT_VIEW, array($this, 'viewListener'));
    }
}
