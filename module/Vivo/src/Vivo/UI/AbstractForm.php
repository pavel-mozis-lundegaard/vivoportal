<?php
namespace Vivo\UI;

use Zend\Form\FormInterface;
use Zend\Form\Form as ZfForm;
use Zend\Http\PhpEnvironment\Request;

/**
 * Form
 * Base abstract Vivo Form
 */
abstract class AbstractForm extends Component
{
    /**
     * @var ZfForm
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Has the data been loaded from request?
     * @var bool
     */
    protected $dataLoaded   = false;

    /**
     * Constructor
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request  = $request;
        $this->form     = $this->createForm();
    }

    /**
     * Creates ZF form and returns it
     * Factory method
     * @return ZfForm
     */
    abstract protected function createForm();

    /**
     * Loads data into the form from the HTTP request
     * Loads GET as well as POST data (POST wins)
     */
    public function loadFromRequest()
    {
        if ($this->dataLoaded) {
            return;
        }
        $data   = $this->request->getQuery()->toArray();
        $data   = array_merge($data, $this->request->getPost()->toArray());
        $this->form->setData($data);
        $this->dataLoaded   = true;
    }

    /**
     * Validates a single form field
     * Facilitates single field AJAX validations
     * @param string $fieldName in array notation (eg 'fieldset1[fieldset2][fieldname]')
     * @param array $messages Validation messages are returned in this array
     * @return boolean
     */
    public function isFieldValid($fieldName, array &$messages)
    {
        $fieldSpec  = $this->getFieldSpecFromArrayNotation($fieldName);
        $this->form->setValidationGroup($fieldSpec);
        $this->loadFromRequest();
        //Store the current bind on validate flag setting and set it to manual
        $bindOnValidateFlag = $this->form->bindOnValidate();
        $this->form->setBindOnValidate(FormInterface::BIND_MANUAL);
        //Validate the field
        $valid  = $this->form->isValid();
        //Restore the original bind on validate setting
        $this->form->setBindOnValidate($bindOnValidateFlag);
        if (!$valid) {
            $formMessages   = $this->form->getMessages();
            $fieldMessages  = $this->getFieldMessages($formMessages, $fieldName);
            $messages   = array_merge($messages, $fieldMessages);
        }
        //Reset the validation group to whole form
        $this->form->setValidationGroup(FormInterface::VALIDATE_ALL);
        return $valid;
    }

    /**
     * Returns field specification from an array notation of a field
     * For 'fieldset1[fieldset2][fieldname]' returns
     * array(
     *      'fieldset1' => array(
     *          'fieldset2' => array('fieldname')
     *      )
     * )
     * For 'fieldname' returns array('fieldname')
     * @param string $arrayNotation
     * @return array
     */
    protected function getFieldSpecFromArrayNotation($arrayNotation)
    {
        $parts          = $this->getPartsFromArrayNotation($arrayNotation);
        $fieldName      = array_pop($parts);
        $fieldSpec      = array($fieldName);
        $reversed   = array_reverse($parts);
        foreach ($reversed as $fieldset) {
            $fieldSpec  = array($fieldset => $fieldSpec);
        }
        return $fieldSpec;
    }

    /**
     * Returns error messages related to the specified field
     * @param array $messages Error messages for the whole form
     * @param string $fieldName In array notation ('fieldset1[fieldset2][fieldname]')
     * @return array
     */
    protected function getFieldMessages(array $messages, $fieldName)
    {
        $parts  = $this->getPartsFromArrayNotation($fieldName);
        foreach ($parts as $part) {
            if (!array_key_exists($part, $messages)) {
                return array();
            }
            $messages   = $messages[$part];
        }
        return $messages;
    }

    /**
     * Returns array of parts exploded from array notation
     * For 'fieldset1[fieldset2][fieldname]' returns array('fieldset1', 'fieldset2', 'fieldname')
     * @param string $arrayNotation
     * @return array
     */
    protected function getPartsFromArrayNotation($arrayNotation)
    {
        $arrayNotation  = str_replace(']', '', $arrayNotation);
        $parts          = explode('[', $arrayNotation);
        return $parts;
    }
}
