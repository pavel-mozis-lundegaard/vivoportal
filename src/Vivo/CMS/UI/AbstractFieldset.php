<?php
namespace Vivo\CMS\UI;

use Vivo\UI\AbstractForm as AbstractVivoForm;
use Vivo\UI\AbstractFieldset as AbstractVivoFieldset;
use Vivo\UI\ZfFieldsetProviderInterface;
use Vivo\UI\Component as UiComponent;
use Vivo\UI\ComponentEventInterface;

use Zend\Form\Fieldset as ZfFieldset;
use Zend\Form\Form as ZfForm;


/**
 * AbstractFieldset
 */
abstract class AbstractFieldset extends AbstractVivoFieldset
{
    /**
     * Parent UI component containing the closest parent ZfFieldset
     * @var ZfFieldsetProviderInterface
     */
    private $parentFieldsetComponent;

    /**
     * Parent AbstractVivoForm UI component
     * @var AbstractVivoForm
     */
    private $parentFormComponent;

    /**
     * Fieldset data or an empty array when validation has not been performed yet
     * @var array
     */
    protected $fieldsetData    = array();

    /**
     * Finds and returns the closest parent component containing a ZFFieldset
     * If not found, returns null
     * @return ZfFieldsetProviderInterface|null
     */
    protected function getParentFieldsetComponent()
    {
        if (is_null($this->parentFieldsetComponent)) {
            $parent = $this;
            while ($parent = $parent->getParent()) {
                if ($parent instanceof ZfFieldsetProviderInterface) {
                    $this->parentFieldsetComponent  = $parent;
                    break;
                }
            }
        }
        return $this->parentFieldsetComponent;
    }

    /**
     * Returns parent ZfFieldset or null when not found
     * @return null|ZfFieldset
     */
    protected function getParentZfFieldset()
    {
        $parentFieldsetComponent    = $this->getParentFieldsetComponent();
        if ($parentFieldsetComponent) {
            $parentZfFieldset   = $parentFieldsetComponent->getZfFieldset();
        } else {
            $parentZfFieldset   = null;
        }
        return $parentZfFieldset;
    }

    /**
     * Finds and returns the parent AbstractForm UI component
     * If not found, returns null
     * @return AbstractForm|null
     */
    protected function getParentFormComponent()
    {
        if (is_null($this->parentFormComponent)) {
            $this->parentFormComponent  = $this->getParent('Vivo\UI\AbstractForm');
        }
        return $this->parentFormComponent;
    }

    /**
     * Returns parent ZfForm or null when not found
     * @return null|ZfForm
     */
    protected function getParentZfForm()
    {
        $parentFormComponent    = $this->getParentFormComponent();
        if ($parentFormComponent) {
            $parentZfForm   = $parentFormComponent->getForm();
        } else {
            $parentZfForm   = null;
        }
        return $parentZfForm;
    }

    /**
     * Init listener
     * Finds closest parent fieldset/form component and attaches to it
     */
    public function initAddSelfToParentFieldset()
    {
        $parentZfFieldset   = $this->getParentZfFieldset();
        if ($parentZfFieldset) {
            //A super fieldset found, add this ZfFieldset to the parent ZfFieldset
            $parentZfFieldset->add($this->getFieldset());
        }
    }

    /**
     * Returns fieldset data or an empty array if the validation has not been done yet
     * @param bool $recursive Should also data from child fieldsets be returned?
     * @return array
     */
    public function getFieldsetData($recursive = true)
    {
        $data   = $this->fieldsetData;
        if ($recursive) {
            foreach ($this->components as $component) {
                if ($component instanceof AbstractFieldset) {
                    $childName  = $component->getUnwrappedZfFieldsetName();
                    $childData  = $component->getFieldsetData($recursive);
                    $data[$childName]   = $childData;
                }
            }
        }
        return $data;
    }

    /**
     * Sets fieldset data
     * @param array $fieldsetData
     */
    public function setFieldsetData(array $fieldsetData)
    {
        foreach ($this->components as $component) {
            if ($component instanceof AbstractFieldset) {
                $unwrapped          = $component->getUnwrappedZfFieldsetName();
                if (isset($fieldsetData[$unwrapped])) {
                    $childData  = $fieldsetData[$unwrapped];
                    //Remove the child data
                    unset($fieldsetData[$unwrapped]);
                } else {
                    $childData  = array();
                }
                $component->setFieldsetData($childData);
            }
        }
        $this->fieldsetData    = $fieldsetData;
    }

    /**
     * Returns unwrapped fieldset name (without eny form/fieldset prefixes)
     * Instead of 'personal[contact][tel]' returns 'tel'
     * For 'tel' returns 'tel'
     * @return string
     */
    public function getUnwrappedZfFieldsetName()
    {
        $wrappedFsName      = $this->getZfFieldset()->getName();
        $arrayNotation      = str_replace(']', '', $wrappedFsName);
        $parts              = explode('[', $arrayNotation);
        $unwrapped          = array_pop($parts);
        return $unwrapped;
    }

    /**
     * View listener
     * Adds fieldset data to view model
     */
    public function viewListenerSetFieldsetData()
    {
        $viewModel                  = $this->getView();
        $viewModel->fieldsetData    = $this->getFieldsetData();
    }

    /**
     * Attaches listeners
     * @return void
     */
    public function attachListeners()
    {
        parent::attachListeners();
        $eventManager   = $this->getEventManager();
        //Init
        $eventManager->attach(ComponentEventInterface::EVENT_INIT,
            array($this, 'initAddSelfToParentFieldset'));
        //View
        $eventManager->attach(ComponentEventInterface::EVENT_VIEW,
            array($this, 'viewListenerSetFieldsetData'));
    }
}
