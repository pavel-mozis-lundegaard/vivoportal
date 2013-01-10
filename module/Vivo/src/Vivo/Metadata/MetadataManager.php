<?php
namespace Vivo\Metadata;

use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\Module\ModuleNameResolver;
use Vivo\Module\Exception\ResourceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Reader\Ini as ConfigReader;

class MetadataManager
{
    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceManager;
    /**
     * @var \Vivo\Module\ResourceManager\ResourceManager
     */
    protected $resourceManager;
    /**
     * @var \Vivo\Module\ModuleNameResolver
     */
    protected $moduleNameResolver;

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceManager
     * @param \Vivo\Module\ResourceManager\ResourceManager $resourceManager
     * @param \Vivo\Module\ModuleNameResolver $moduleNameResolver
     */
    public function __construct(ServiceLocatorInterface $serviceManager, ResourceManager $resourceManager, ModuleNameResolver $moduleNameResolver) {
        $this->serviceManager = $serviceManager;
        $this->resourceManager = $resourceManager;
        $this->moduleNameResolver = $moduleNameResolver;
    }

    /**
     * @return array
     */
    public function getRawMetadata($entity) {
        $config = array();
        $className = $parent = get_class($entity);
        $parents = array($parent);
        while(($parent = get_parent_class($parent)) && $parent !== false) {
            $parents[] = $parent;
        }

        // Vivo CMS model, other is a module model
        if(strpos($className, 'Vivo\\') === 0) {
            echo $className;
        }
        else {


            $parents = array_reverse($parents);
            $reader = new ConfigReader();

            foreach ($parents as $class) {
                $moduleName = $this->moduleNameResolver->fromFqcn($class);
                $path = sprintf('%s.ini', str_replace('\\', DIRECTORY_SEPARATOR, $class));

                try {
                    $resource = $this->resourceManager->getResource($moduleName, $path, 'metadata');

                    $entityConfig = $reader->fromString($resource);
                    $config = array_merge($config, $entityConfig);
                }
                catch (ResourceNotFoundException $e) { }
            }
        }

        print_r($config);
        // $proper = $entity...

        // if(string z metada class exists...
        //   if(class instance of ProviderI)

        // $prov = new ClassMetadatPro(locator)


        // Medatadata
        // $configArray = $...


        // return $configArray;
    }

    /**
     * @return array
     */
    public function getMetadata($entity) {
        return $this->getRawMetadata($entity);
    }
}
