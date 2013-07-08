<?php
namespace Vivo\CMS\UI;

use Vivo\UI\AbstractFieldset as AbstractVivoFieldset;
use Vivo\UI\ZfFieldsetProviderInterface;
use Vivo\UI\Component as UiComponent;

use Zend\Form\Fieldset as ZfFieldset;


/**
 * AbstractFieldset
 */
abstract class AbstractFieldset extends AbstractVivoFieldset
{
    /**
     * Parent UI component containing the closest parent ZfFieldset
     * @var UiComponent
     */
    protected $parentFieldsetComponent;

    /**
     * Parent ZfFieldset
     * @var ZfFieldset
     */
    protected $parentZfFieldset;

    public function init()
    {
        parent::init();
        $this->parentFieldsetComponent  = $this->getParentFieldsetComponent();
        if ($this->parentFieldsetComponent) {
            //A super fieldset found
            $this->parentZfFieldset         = $this->parentFieldsetComponent->getZfFieldset();
            //Add this ZfFieldset to the parent ZfFieldset
            $this->parentZfFieldset->add($this->getFieldset());
        }
    }

    /**
     * Finds and returns the closest parent component containing a ZFFieldset
     * If not found, returns null
     * @return UiComponent|null
     */
    protected function getParentFieldsetComponent()
    {
        $parent = $this;
        while ($parent = $parent->getParent()) {
            if ($parent instanceof ZfFieldsetProviderInterface) {
                return $parent;
            }
        }
        return null;
    }
}
