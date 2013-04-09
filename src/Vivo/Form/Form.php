<?php
namespace Vivo\Form;

use Vivo\Form\Factory;

use Zend\Form\Form as ZendForm;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Form extends ZendForm
{

    public function getFormFactory()
    {
        if (null === $this->factory) {
            $this->setFormFactory(new Factory());
        }

        return $this->factory;
    }

    /**
     * Retrieve input filter used by this form
     * @return null|InputFilterInterface
     */
    public function getInputFilter()
    {
        if ($this->object instanceof InputFilterAwareInterface) {
            if (null == $this->baseFieldset) {
                $this->filter = $this->object->getInputFilter();
            } else {
                $name = $this->baseFieldset->getName();
                if (!$this->filter instanceof InputFilterInterface || !$this->filter->has($name)) {
                    $filter = new \Vivo\InputFilter\VivoInputFilter();
                    $filter->add($this->object->getInputFilter(), $name);
                    $this->filter = $filter;
                }
            }
        }

        if (!isset($this->filter)) {
            //Create VivoInputFilter by default
            $this->filter = new \Vivo\InputFilter\VivoInputFilter();
        }

        if (!$this->hasAddedInputFilterDefaults
            && $this->filter instanceof InputFilterInterface
            && $this->useInputFilterDefaults()
        ) {
            $this->attachInputFilterDefaults($this->filter, $this);
            $this->hasAddedInputFilterDefaults = true;
        }

        return $this->filter;
    }
}
