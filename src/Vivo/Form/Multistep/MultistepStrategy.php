<?php
namespace Vivo\Form\Multistep;

use Vivo\Form\Exception;

use Zend\Form\Form;
use Zend\Form\FormInterface;

/**
 * MultistepStrategy
 */
class MultistepStrategy implements MultistepStrategyInterface
{
    /**
     * Array of strategy options
     * @var array
     */
    protected $options  = array(
        //Mapping to real element names
        'element_names'  => array(
            //Element containing the current step name
            'step'      => 'step',
            //Element containing name of the element to go to
            'goto_step' => 'goto_step',
        ),
    );

    /**
     * Array of form steps in order
     * array(
     *      'step_name' => array(
     *          'validation_group'  => <validation group specification sent to Form::setValidationGroup()
     *      ),
     * )
     * @var array
     */
    protected $steps            = array();

    /**
     * The form this multistep strategy operates on
     * @var FormInterface
     */
    protected $form;

    /**
     * Sets the form this multistep strategy operates on
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    /**
     * Sets steps the form has
     * @param array $steps
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     */
    public function setSteps(array $steps)
    {
        foreach ($steps as $stepName => $stepParams) {
            if (!is_string($stepName)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Keys in the steps array must be strings (step names)", __METHOD__));
            }
            if (!isset($stepParams['validation_group'])) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Missing key 'validation_group' in step '%s'", __METHOD__, $stepName));
            }
        }
        $this->steps = $steps;
    }

    /**
     * Modifies the form to be usable as a multi-step form
     */
    public function modifyForm()
    {
        $this->setStep($this->getNextStep());
        $this->resetGotoStep();
    }

    /**
     * Returns next step after the specified one or the first step when $currentStep == null
     * Returns null when $currentStep is the last step
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return null|string
     */
    public function getNextStep($currentStep = null)
    {
        $nextSteps  = $this->getNextSteps($currentStep);
        if (count($nextSteps) == 0) {
            $nextStep   = null;
        } else {
            $nextStep   = array_shift($nextSteps);
        }
        return $nextStep;
    }

    /**
     * Returns array of steps following after the current step
     * For last step returns an empty array
     * When $currentStep == null, returns all steps
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return array()
     */
    public function getNextSteps($currentStep = null)
    {
        $stepKeys   = array_keys($this->steps);
        if (is_null($currentStep)) {
            $steps  = $stepKeys;
        } else {
            if (!$this->isStepNameValid($currentStep)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Step '%s' is not a valid step", __METHOD__, $currentStep));
            }
            $offset = array_search($currentStep, $stepKeys);
            $start  = $offset +1;
            $length = count($stepKeys);
            if ($start < $length) {
                $steps  = array_slice($stepKeys, $start);
            } else {
                //Current step is last step
                $steps  = array();
            }
        }
        return $steps;
    }

    /**
     * Returns step preceding the specified one or the last step when $currentStep == null
     * Returns null when $currentStep is the first step
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return null|string
     */
    public function getPreviousStep($currentStep = null)
    {
        $previousSteps  = $this->getPreviousSteps($currentStep);
        if (count($previousSteps) == 0) {
            $previousStep   = null;
        } else {
            $previousStep   = array_pop($previousSteps);
        }
        return $previousStep;
    }

    /**
     * Returns array of steps preceding the current step
     * For first step returns an empty array
     * When $currentStep == null, returns all steps
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return array()
     */
    public function getPreviousSteps($currentStep = null)
    {
        $stepKeys   = array_keys($this->steps);
        if (is_null($currentStep)) {
            $steps  = $stepKeys;
        } else {
            if (!$this->isStepNameValid($currentStep)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Step '%s' is not a valid step", __METHOD__, $currentStep));
            }
            $offset = array_search($currentStep, $stepKeys);
            $length = $offset;
            $steps  = array_slice($stepKeys, 0, $length);
        }
        return $steps;

    }

    /**
     * Returns if the specified value is a valid step
     * @param string $step
     * @return bool
     */
    public function isStepNameValid($step)
    {
        return array_key_exists($step, $this->steps);
    }

    /**
     * Returns validation group for the current step
     * @return mixed
     */
    public function getValidationGroup()
    {
        $step               = $this->getStep();
        $validationGroup    = $this->assembleValidationGroupForStep($step);
        return $validationGroup;
    }

    /**
     * Returns validation group specification which should be used for the given step
     * Assembles validation groups for the given step and all preceding steps
     * @param string $step
     * @throws \Vivo\Form\Exception\RuntimeException
     * @return array
     */
    protected function assembleValidationGroupForStep($step)
    {
        if (!$this->isStepNameValid($step)) {
            throw new Exception\RuntimeException(sprintf("%s: Invalid step name '%s'", __METHOD__, $step));
        }
        $stepKeys           = $this->getPreviousSteps($step);
        $stepKeys[]         = $step;
        $validationGroup    = array();
        foreach ($stepKeys as $stepKey) {
            $validationGroup[]  = $this->steps[$stepKey]['validation_group'];
        }
        return $validationGroup;
    }

    /**
     * Returns current step identification from form
     * @throws \Vivo\Form\Exception\RuntimeException
     * @return string
     */
    public function getStep()
    {
        $this->checkFormIsSet();
        $this->form->setValidationGroup($this->options['element_names']['step']);
        if (!$this->form->isValid()) {
            throw new Exception\RuntimeException(
                sprintf("%s: Form element containing step '%s' is not valid",
                    __METHOD__, $this->options['element_names']['step']));
        }
        $data   = $this->form->getData();
        $step   = $data[$this->options['element_names']['step']];
        //If the form has not been submitted yet, there are no data to validate and $step is null,
        //get the value preset in the form
        if (is_null($step)) {
            $step   = $this->form->get($this->options['element_names']['step'])->getValue();
        }
        return $step;
    }

    /**
     * Sets step identification into the form
     * @param string $step
     * @throws \Vivo\Form\Exception\RuntimeException
     */
    public function setStep($step)
    {
        $this->checkFormIsSet();
        if (!$this->isStepNameValid($step)) {
            throw new Exception\RuntimeException(sprintf("%s: Invalid step name '%s'", __METHOD__, $step));
        }
        if (!$this->form->has($this->options['element_names']['step'])) {
            //Step
            $this->form->add(array(
                'name'  => $this->options['element_names']['step'],
                'type'  => 'hidden',
            ));
        }
        //Set step
        $this->form->get($this->options['element_names']['step'])->setValue($step);
    }

    /**
     * Returns identification of the step to go to from the form
     * @throws \Vivo\Form\Exception\RuntimeException
     * @return string
     */
    public function getGotoStep()
    {
        $this->checkFormIsSet();
        $this->form->setValidationGroup($this->options['element_names']['goto_step']);
        if (!$this->form->isValid()) {
            throw new Exception\RuntimeException(
                sprintf("%s: Form element containing goto_step '%s' is not valid",
                    __METHOD__, $this->options['element_names']['goto_step']));
        }
        $data       = $this->form->getData();
        $gotoStep   = $data[$this->options['element_names']['goto_step']];
        //If the form has not been submitted yet, there are no data to validate and $gotoStep is null,
        //get the value preset in the form
        if (is_null($gotoStep)) {
            $gotoStep   = $this->form->get($this->options['element_names']['goto_step'])->getValue();
        }
        return $gotoStep;
    }

    /**
     * Resets the goto_step hidden field
     */
    public function resetGotoStep()
    {
        $this->checkFormIsSet();
        if (!$this->form->has($this->options['element_names']['goto_step'])) {
            $this->form->add(array(
                'name'  => $this->options['element_names']['goto_step'],
                'type'  => 'hidden',
            ));
        }
        //Set goto_step
        $this->form->get($this->options['element_names']['goto_step'])->setValue('');
    }

    /**
     * Advances one step forward
     * If there are no more steps, returns null, otherwise returns name of the next step
     * @return string|null
     */
    public function next()
    {
        $step       = $this->getStep();
        $nextStep   = $this->getNextStep($step);
        if ($nextStep) {
            $this->setStep($nextStep);
        }
        return $nextStep;
    }

    /**
     * Returns one step back
     * If there are no more steps, returns null, otherwise returns name of the previous step
     * @return string|null
     */
    public function back()
    {
        $step           = $this->getStep();
        $previousStep   = $this->getPreviousStep($step);
        if ($previousStep) {
            $this->setStep($previousStep);
        }
        return $previousStep;
    }

    /**
     * Checks that a form has been set into this multistep strategy
     * @throws \Vivo\Form\Exception\RuntimeException
     */
    protected function checkFormIsSet()
    {
        if (!$this->form) {
            throw new Exception\RuntimeException(sprintf("%s: No form is set in multi-step strategy", __METHOD__));
        }
    }
}
