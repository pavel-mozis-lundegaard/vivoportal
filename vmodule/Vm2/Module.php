<?php
namespace Vm2;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Vivo\Vmodule\AutoloaderNs' => array(
                'stream_name'   => \Vivo\Vmodule\StreamWrapper::STREAM_NAME,
                'namespaces' => array(
                    __NAMESPACE__ => '/' . __NAMESPACE__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
