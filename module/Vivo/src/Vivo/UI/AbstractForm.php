<?php
namespace Vivo\UI;

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
     * @param string $fieldName in array notation (eg form[fieldset1][fieldset2][fieldname])
     * @return boolean
     */
    public function isFieldValid($fieldName)
    {
        //TODO - continue here...





    }
}
