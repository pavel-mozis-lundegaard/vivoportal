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
