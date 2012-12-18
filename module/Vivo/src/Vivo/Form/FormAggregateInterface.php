<?php
namespace Vivo\Form;

use Zend\Form\FormInterface as ZendFormInterface;

interface FormAggregateInterface
{
    /**
     * @return ZendFormInterface
     */
    public function getForm();
}
