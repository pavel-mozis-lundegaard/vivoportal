<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Form as ZfForm;
use Zend\Form\Fieldset;
use Zend\Form\Element;


/**
 * VivoForm
 * Form view helper renders complete form
 */
class VivoForm extends AbstractHelper
{
    /**
     * Invoke the helper as a PhpRenderer method call
     * @param \Zend\Form\Form $form
     * @return VivoForm
     */
    public function __invoke(ZfForm $form = null)
    {
        if (!$form) {
            return $this;
        }
        return $this->render($form);
    }

    /**
     * Renders the form
     * @param \Zend\Form\Form $form
     * @return string
     */
    public function render(ZfForm $form)
    {
        /** @var $formVh \Zend\Form\View\Helper\Form */
        $formVh             = $this->getView()->plugin('form');
        /** @var $vivoFormFieldsetVh VivoFormFieldset */
        $vivoFormFieldsetVh = $this->getView()->plugin('vivoFormFieldset');
        $html               = $formVh->openTag($form);
        $html               .= $vivoFormFieldsetVh->render($form, false);
        $html               .= $formVh->closeTag();
        return $html;
    }
}
