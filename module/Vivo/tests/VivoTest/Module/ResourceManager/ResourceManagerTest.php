<?php
namespace VivoTest\Module\ResourceManager;

use Vivo\Module\ResourceManager\ResourceManager;
use Vivo\Module\StorageManager\StorageManager;
use Vivo\Storage\PathBuilder\PathBuilderInterface;

use Zend\ModuleManager\ModuleManager;

/**
 * ResourceManagerTest
 */
class ResourceManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ResourceManager
     */
    protected $resourceManager;

    /**
     * @var StorageManager
     */
    protected $moduleStorageManager;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var array
     */
    protected $options  = array();

    protected function setUp()
    {
        $this->options  = array(
            'type_map'      => array(
                'view'      => 'view',
                'layout'    => 'view/layout',
                'resource'  => 'resource',
            ),
            'default_type'  => 'resource',
        );
        $this->pathBuilder          = $this->getMock('Vivo\Storage\PathBuilder\PathBuilderInterface',
                                                     array(), array(), '', false);
        $this->moduleStorageManager = $this->getMock('Vivo\Module\StorageManager\StorageManager',
                                                     array(), array(), '', false);
        $this->moduleStorageManager->expects($this->any())
            ->method('getPathBuilder')
            ->will($this->returnValue($this->pathBuilder));
        $this->moduleManager        = $this->getMock('Zend\ModuleManager\ModuleManager', array(), array(), '', false);
        $this->resourceManager  = new ResourceManager($this->moduleStorageManager, $this->options);
        $this->resourceManager->setModuleManager($this->moduleManager);
    }

    public function testGetResource()
    {
        $moduleName = 'MyModule1';
        $pathToRsc  = 'abc/def/ghi.xxx';
        $type       = 'layout';
        $fullPath   = '/foo/bar/baz/xyz';
        $rscData    = 'lorem ipsum';
        $module     = $this->getMock('stdClass', array(), array(), '', false);
        $this->moduleManager->expects($this->once())
            ->method('getModule')
            ->with($moduleName)
            ->will($this->returnValue($module));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->with(array($this->options['type_map'][$type], $pathToRsc), false)
            ->will($this->returnValue($fullPath));
        $this->moduleStorageManager->expects($this->once())
            ->method('getFileData')
            ->with($moduleName, $fullPath)
            ->will($this->returnValue($rscData));
        $rscDataRead    = $this->resourceManager->getResource($moduleName, $pathToRsc, $type);
        $this->assertEquals($rscData, $rscDataRead);
    }

    public function testGetResourceStream()
    {
        $moduleName = 'MyModule1';
        $pathToRsc  = 'abc/def/ghi.xxx';
        $type       = 'layout';
        $fullPath   = '/foo/bar/baz/xyz';
        $module     = $this->getMock('stdClass', array(), array(), '', false);
        $stream     = $this->getMock('Vivo\IO\FileInputStream', array(), array(), '', false);
        $this->moduleManager->expects($this->once())
            ->method('getModule')
            ->with($moduleName)
            ->will($this->returnValue($module));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->with(array($this->options['type_map'][$type], $pathToRsc), false)
            ->will($this->returnValue($fullPath));
        $this->moduleStorageManager->expects($this->once())
            ->method('getFileStream')
            ->with($moduleName, $fullPath)
            ->will($this->returnValue($stream));
        $streamRead = $this->resourceManager->getResourceStream($moduleName, $pathToRsc, $type);
        $this->assertSame($stream, $streamRead);
    }


}
