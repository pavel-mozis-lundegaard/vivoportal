<?php
namespace Vivo\Service;

use Vivo\Http\StreamResponse;

use Zend\Console\Response as ConsoleResponse;
use Zend\Console\Console;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResponseFactory implements FactoryInterface
{
    /**
     * Create and return a response instance, according to current environment.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Stdlib\Message
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (Console::isConsole()) {
            return new ConsoleResponse();
        }
        $response = new StreamResponse();
        $response->getHeaders()->addHeaderLine('X-Generated-By: Vivo')
        ->addHeaderLine(
                'X-Generated-At: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
        return $response;
    }
}
