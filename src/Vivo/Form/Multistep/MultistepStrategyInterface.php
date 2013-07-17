<?php
namespace Vivo\Form\Multistep;

use Vivo\Form\Exception;

use Zend\Form\Form;

/**
 * MultistepStrategyInterface
 */
interface MultistepStrategyInterface
{
    /**
     * Sets steps the form has
     * @param array $steps
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     */
    public function setSteps(array $steps);

    /**
     * Modifies the form to be usable as a multi-step form
     * @param Form $form
     */
    public function modifyForm(Form $form);

    /**
     * Returns if the specified value is a valid step
     * @param string $step
     * @return bool
     */
    public function isStepNameValid($step);

    /**
     * Returns step identification from form
     * @param Form $form
     * @return string
     */
    public function getStep(Form $form);

    /**
     * Sets step identification into the form
     * @param Form $form
     * @param string $step
     * @throws \Vivo\Form\Exception\RuntimeException
     */
    public function setStep(Form $form, $step);

    /**
     * Returns identification of the step to go to from the form
     * @param Form $form
     * @throws \Vivo\Form\Exception\RuntimeException
     * @return string
     */
    public function getGotoStep(Form $form);

    /**
     * Returns array of steps following after the current step
     * For last step returns an empty array
     * When $currentStep == null, returns all steps
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return array()
     */
    public function getNextSteps($currentStep = null);

    /**
     * Returns next step after the specified one or the first step when $currentStep == null
     * Returns null when $currentStep is the last step
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return null|string
     */
    public function getNextStep($currentStep = null);

    /**
     * Returns array of steps preceding the current step
     * For first step returns an empty array
     * When $currentStep == null, returns all steps
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return array()
     */
    public function getPreviousSteps($currentStep = null);

    /**
     * Returns step preceding the specified one or the last step when $currentStep == null
     * Returns null when $currentStep is the first step
     * @param string|null $currentStep
     * @throws \Vivo\Form\Exception\InvalidArgumentException
     * @return null|string
     */
    public function getPreviousStep($currentStep = null);

    /**
     * Returns validation group for the current step
     * @param \Zend\Form\Form $form
     * @return mixed
     */
    public function getValidationGroup(Form $form);

    /**
     * Resets the goto_step hidden field
     * @param Form $form
     */
    public function resetGotoStep(Form $form);

    /**
     * Advances one step forward
     * If there are no more steps, returns null, otherwise returns name of the next step
     * @param Form $form
     * @return string|null
     */
    public function next(Form $form);

    /**
     * Returns one step back
     * If there are no more steps, returns null, otherwise returns name of the previous step
     * @param Form $form
     * @return string|null
     */
    public function back(Form $form);
}