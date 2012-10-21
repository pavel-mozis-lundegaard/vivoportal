<?php
namespace Vm1;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            /*
            'Vivo\Module\AutoloaderClassMap' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            */
            'Vivo\Module\AutoloaderNs' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}