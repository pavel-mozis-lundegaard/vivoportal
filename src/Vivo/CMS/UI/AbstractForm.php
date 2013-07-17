<?php
namespace Vivo\CMS\UI;

use Vivo\UI\AbstractForm as AbstractVivoForm;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Event\CMSEvent;
use Vivo\Service\Initializer\CmsEventAwareInterface;
use Vivo\UI\ComponentEventInterface;

use Zend\Form\FormInterface;

/**
 * AbstractForm
 * Abstract CMS Form
 */
abstract class AbstractForm extends AbstractVivoForm implements InjectModelInterface,
                                                                CmsEventAwareInterface
{
    /**
     * @var Content
     */
    protected $content;

    /**
     * @var Document
     */
    protected $document;

    /**
     * CMS Event
     * @var CMSEvent
     */
    protected $cmsEvent;

    /**
     * Form data or an empty array when validation has not been performed yet
     * @var array
     */
    protected $formData     = array();

    /**
     * Whether or not validation has occurred
     * @var bool
     */
    protected $hasValidated = false;

    /**
     * Result of last validation operation
     * @var bool
     */
    protected $isValid      = false;

    /**
     * Sets content
     * @param Content $content
     * @return void
     */
    public function setContent(Content $content)
    {
        $this->content  = $content;
    }

    /**
     * Sets document
     * @param Document $document
     * @return void
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Sets the CMS event
     * @param CMSEvent $cmsEvent
     * @return void
     */
    public function setCmsEvent(CMSEvent $cmsEvent)
    {
        $this->cmsEvent = $cmsEvent;
    }
    /**
     * Check if the form has been validated
     * @return bool
     */
    public function hasValidated()
    {
        return $this->hasValidated;
    }

    /**
     * Validates the form and returns the validation result
     * @param bool $revalidate Force revalidation of the form even though it has been validated before
     * @param mixed $validationGroup If not set, $this->getValidationGroup() will be used to provide the VG
     * @return bool
     */
    public function isValid($revalidate = false, $validationGroup = null)
    {
        if ($revalidate) {
            $this->hasValidated = false;
        }
        if ($this->hasValidated) {
            return $this->isValid;
        }
        if (is_null($validationGroup)) {
            $validationGroup    = $this->getValidationGroup();
        }
        $this->isValid      = false;
        $this->formData     = array();
        $form               = $this->getForm();
        $form->setValidationGroup($validationGroup);
        $this->isValid      = $form->isValid();
        $this->hasValidated = true;
        $formData           = $form->getData();
        $this->setFormData($formData);
        return $this->isValid;
    }

    /**
     * Returns validation group which should be used for validation
     * Descendants may redefine if needed
     * @return mixed
     */
    protected function getValidationGroup()
    {
        if ($this->multistepStrategy) {
            $zfForm             = $this->getForm();
            $validationGroup    = $this->multistepStrategy->getValidationGroup($zfForm);
            if ($this->autoAddCsrf && ($validationGroup != FormInterface::VALIDATE_ALL) && (
                    (is_array($validationGroup) && !in_array($this->autoCsrfFieldName, $validationGroup))
                    || (is_string($validationGroup) && $validationGroup != $this->autoCsrfFieldName))) {
                //Csrf field is missing in validation group, add it
                if (!is_array($validationGroup)) {
                    $validationGroup    = array($validationGroup);
                }
                $validationGroup[]  = $this->autoCsrfFieldName;
            }
        } else {
            $validationGroup    = FormInterface::VALIDATE_ALL;
        }
        return $validationGroup;
    }

    /**
     * Returns form data
     * @param bool $recursive Return also data from child fieldsets?
     * @throws Exception\DomainException
     * @return array
     */
    public function getData($recursive = true)
    {
        if (!$this->hasValidated) {
            throw new Exception\DomainException(sprintf('%s: cannot return data as validation has not yet occurred',
                __METHOD__));
        }
        $data   = $this->formData;
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
     * Sets form data
     * @param array $formData
     */
    protected function setFormData(array $formData)
    {
        foreach ($this->components as $component) {
            if ($component instanceof AbstractFieldset) {
                $unwrapped          = $component->getUnwrappedZfFieldsetName();
                if (isset($formData[$unwrapped])) {
                    $childData  = $formData[$unwrapped];
                    //Remove the child data
                    unset($formData[$unwrapped]);
                } else {
                    $childData  = array();
                }
                $component->setFieldsetData($childData);
            }
        }
        $this->formData    = $formData;
    }

    /**
     * View listener
     */
    public function viewListenerSetContentAndDocument() {
        $this->view->content           = $this->content;
        $this->view->document          = $this->document;
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
        $eventManager->attach(ComponentEventInterface::EVENT_VIEW, array($this, 'viewListenerSetContentAndDocument'));
    }

}
