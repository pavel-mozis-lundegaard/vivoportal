<?php
namespace Vivo\Service\Initializer;

use Zend\Stdlib\RequestInterface;

/**
 * Interaface for injecting Request
 */
interface RequestAwareInterface
{
    public function setRequest(RequestInterface $request);
}
