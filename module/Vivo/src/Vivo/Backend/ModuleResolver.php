<?php
namespace Vivo\Backend;

/**
 * ModuleResolver loads information of registered modules from config.
 *
 */
class ModuleResolver implements \Zend\ServiceManager\ServiceManagerAwareInterface
{

    /**
     * Configuration of backend modules.
     * @var array
     */
    protected $config = array();

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Sets backend modules config;
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Returns array of registered modules information.
     * @return array
     */
    public function getModules()
    {
        return $this->config;
    }

    /**
     * Returns class name of UI component.
     *
     * @param string $name
     * @return string
     * @throws Exception\Exception
     */
    public function getModuleComponentClass($name)
    {
        if (!isset($this->config[$name])) {
            throw new Exception\Exception(sprintf('%s: Backend module `%s` not found.', __METHOD__, $name));
        }
        return $this->config[$name]['componentClass'];
    }

    /**
     * Return instance of UI component for backend module.
     * @param string $name
     * @return \Vivo\UI\Component
     */
    public function createComponent($name)
    {
        return $this->serviceManager->create($this->getModuleComponentClass($name));
    }

    /**
     * Inject service manager.
     * @param \Zend\ServiceManager\ServiceManager $serviceManager
     */
    public function setServiceManager(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }
}
