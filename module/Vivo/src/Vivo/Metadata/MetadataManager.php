<?php
namespace Vivo\Metadata;

use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\Module\ModuleNameResolver;
use Vivo\Module\Exception\ResourceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Config\Reader\Ini as ConfigReader;
use Zend\Config\Config;

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
     * @param object $entity
     * @return array
     */
    public function getRawMetadata($entity) {
        $config = new Config(array());
        $reader = new ConfigReader();

        $className = $parent = get_class($entity);
        $parents = array($parent);
        while(($parent = get_parent_class($parent)) && $parent !== false) {
            $parents[] = $parent;
        }
        $parents = array_reverse($parents);

        foreach ($parents as $class) {
            // Vivo CMS model, other is a module model
            if(strpos($class, 'Vivo\\') === 0) {
                //@todo: path
                $path = realpath(sprintf('%s/../../../config/metadata/%s.ini', __DIR__, str_replace('\\', DIRECTORY_SEPARATOR, $class)));

                if($path) {
                    $resource = file_get_contents($path);
                    $entityConfig = $reader->fromString($resource);
                    $entityConfig = new Config($entityConfig);

                    $config = $config->merge($entityConfig);
                }
            }
            else {
                $moduleName = $this->moduleNameResolver->fromFqcn($class);
                $path = sprintf('%s.ini', str_replace('\\', DIRECTORY_SEPARATOR, $class));

                try {
                    $resource = $this->resourceManager->getResource($moduleName, $path, 'metadata');
                    $entityConfig = $reader->fromString($resource);
                    $entityConfig = new Config($entityConfig);

                    $config = $config->merge($entityConfig);
                }
                catch (ResourceNotFoundException $e) { }
            }
        }

        $config = $config->toArray();

        return $config;
    }

    /**
     * @return array
     */
    public function getMetadata($entity) {
        // $proper = $entity...

        // if(string z metada class exists...
        //   if(class instance of ProviderI)

        // $prov = new ClassMetadatPro(locator)


        // Medatadata
        // $configArray = $...

        return $this->getRawMetadata($entity);
    }
}
