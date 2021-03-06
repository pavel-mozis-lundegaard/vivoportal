<?php
namespace Vivo\Form;

use Vivo\Form\Factory;
use Vivo\InputFilter\VivoInputFilter;

use Zend\Form\Form as ZendForm;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\FieldsetInterface;
use Zend\InputFilter\InputProviderInterface;

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
                    $filter = new VivoInputFilter();
                    $filter->add($this->object->getInputFilter(), $name);
                    $this->filter = $filter;
                }
            }
        }

        if (!isset($this->filter)) {
            //Create VivoInputFilter by default
            $this->filter = new VivoInputFilter();
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

    /**
     * Attach defaults provided by the elements to the input filter
     *
     * @param  InputFilterInterface $inputFilter
     * @param  FieldsetInterface $fieldset Fieldset to traverse when looking for default inputs
     * @return void
     */
    public function attachInputFilterDefaults(InputFilterInterface $inputFilter, FieldsetInterface $fieldset)
    {
        $formFactory  = $this->getFormFactory();
        $inputFactory = $formFactory->getInputFilterFactory();

        if ($this instanceof InputFilterProviderInterface) {
            $inputFilterSpecification   = $this->getInputFilterSpecification();
            if ($inputFactory instanceof \Vivo\InputFilter\Factory) {
                $inputFactory->addConditionsFromSpecification($inputFilter, $inputFilterSpecification);
                $inputFactory->addAtLeastGroupsFromSpecification($inputFilter, $inputFilterSpecification);
            }
            foreach ($inputFilterSpecification as $name => $spec) {
                $input = $inputFactory->createInput($spec);
                $inputFilter->add($input, $name);
            }
        }

        foreach ($fieldset->getElements() as $element) {
            $name = $element->getName();

            if ($this->preferFormInputFilter && $inputFilter->has($name)) {
                continue;
            }

            if (!$element instanceof InputProviderInterface) {
                if ($inputFilter->has($name)) {
                    continue;
                }
                // Create a new empty default input for this element
                $spec = array('name' => $name, 'required' => false);
            } else {
                // Create an input based on the specification returned from the element
                $spec  = $element->getInputSpecification();
            }

            $input = $inputFactory->createInput($spec);
            $inputFilter->add($input, $name);
        }

        foreach ($fieldset->getFieldsets() as $fieldset) {
            $name = $fieldset->getName();

            if (!$fieldset instanceof InputFilterProviderInterface) {
                if (!$inputFilter->has($name)) {
                    // Add a new empty input filter if it does not exist (or the fieldset's object input filter),
                    // so that elements of nested fieldsets can be recursively added
                    if ($fieldset->getObject() instanceof InputFilterAwareInterface) {
                        $inputFilter->add($fieldset->getObject()->getInputFilter(), $name);
                    } else {
                        $inputFilter->add(new VivoInputFilter(), $name);
                    }
                }

                $fieldsetFilter = $inputFilter->get($name);

                if (!$fieldsetFilter instanceof InputFilterInterface) {
                    // Input attached for fieldset, not input filter; nothing more to do.
                    continue;
                }

                // Traverse the elements of the fieldset, and attach any
                // defaults to the fieldset's input filter
                $this->attachInputFilterDefaults($fieldsetFilter, $fieldset);
                continue;
            }

            if ($inputFilter->has($name)) {
                // if we already have an input/filter by this name, use it
                continue;
            }

            // Create an input filter based on the specification returned from the fieldset
            $spec   = $fieldset->getInputFilterSpecification();
            $filter = $inputFactory->createInputFilter($spec);
            $inputFilter->add($filter, $name);

            // Recursively attach sub filters
            $this->attachInputFilterDefaults($filter, $fieldset);
        }
    }
}
