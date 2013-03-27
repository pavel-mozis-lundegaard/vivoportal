<?php
namespace Vivo\Service\Initializer;

use Zend\I18n\Translator\Translator;

/**
 * Interface for injecting Translator
 */
interface TranslatorAwareInterface
{
    /**
     * Injects translator
     * @param \Zend\I18n\Translator\Translator $translator
     */
    public function setTranslator(Translator $translator);
}
