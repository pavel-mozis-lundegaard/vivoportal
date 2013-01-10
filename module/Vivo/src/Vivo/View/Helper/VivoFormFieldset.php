<?php
namespace Vivo\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Form\Fieldset;
use Zend\Form\Element;

/**
 * VivoFormFieldset
 * Form fieldset view helper renders a fieldset
 */
class VivoFormFieldset extends AbstractHelper
{
    /**
     * Invoke the helper as a PhpRenderer method call
     * @param \Zend\Form\Fieldset $fieldset
     * @return string
     */
    public function __invoke(Fieldset $fieldset = null)
    {
        if (!$fieldset) {
            return $this;
        }
        return $this->render($fieldset);
    }

    /**
     * Renders the fieldset
     * @param \Zend\Form\Fieldset $fieldset
     * @param bool $renderFieldsetTag
     * @throws Exception\UnsupportedFormItemException
     * @return string
     */
    public function render(Fieldset $fieldset, $renderFieldsetTag = true)
    {
        /** @var $formRowVh \Zend\Form\View\Helper\FormRow */
        $formRowVh      = $this->getView()->plugin('formRow');
        $translator     = $this->getView()->plugin('translate');
        $escaper        = $this->getView()->plugin('escapeHtml');
        $html           = '';
        if ($renderFieldsetTag) {
            $html           .= "\n<fieldset>";
            if ($fieldset->getLabel()) {
                $label      = $escaper($translator($fieldset->getLabel()));
                $html       .= "\n<legend>" . $label. '</legend>';
            }
        }
        foreach ($fieldset as $item) {
            if ($item instanceof Fieldset) {
                //Fieldset
                $html   .= $this->render($item);
            } elseif ($item instanceof Element) {
                //Element
                $html   .= "\n<div class=\"form_element\">";
                $html   .= $formRowVh->render($item);
                $html   .= "\n</div>";
            } else {
                //Unsupported type
                throw new Exception\UnsupportedFormItemException(sprintf("%s: Unsupported form item type '%s'",
                    __METHOD__, get_class($item)));
            }
        }
        if ($renderFieldsetTag) {
            $html           .= "\n</fieldset>";
        }
        return $html;
    }
}
