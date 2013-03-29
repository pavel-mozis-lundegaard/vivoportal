<?php
namespace Vivo\Service\Listener;

use Zend\Mvc\MvcEvent;

/**
 * Class RegisterTemplateResolver
 * @package Vivo\Service\Listener
 */
class RegisterTemplateResolverListener
{
    /**
     * Register template resolver.
     * @param MvcEvent $e
     */
    public function registerTemplateResolver(MvcEvent $e)
    {
        $sm = $e->getTarget()->getServiceManager();
        /* @var $viewResolver \Zend\View\Resolver\AggregateResolver */
        $viewResolver = $sm->get('viewresolver');
        $viewResolver->attach($sm->get('template_resolver'), 100);
    }
}
