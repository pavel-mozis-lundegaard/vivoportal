<?php
namespace Vivo\Service\Initializer;

use Zend\InputFilter\Factory as InputFilterFactory;

/**
 * InputFilterFactoryAwareInterface
 * @package Vivo\InputFilter
 */
interface InputFilterFactoryAwareInterface
{
    /**
     * Sets the input filter factory
     * @param InputFilterFactory $inputFilterFactory
     * @return void
     */
    public function setInputFilterFactory(InputFilterFactory $inputFilterFactory);
}
