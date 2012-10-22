<?php
namespace VivoTest\Module;

use Vivo\Module\ModuleManagerFactory;

/**
 * ModuleManagerFactoryTest
 */
class ModuleManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModuleManagerFactory
     */
    protected $factory;

    protected function setUp()
    {
        $vModulePaths   = array('/abc', '/def/ghi');
        $streamName     = 'vm';
        $this->factory  = new ModuleManagerFactory($vModulePaths, $streamName);
    }

    public function testConstructExceptionOnMissingStreamName()
    {
        $this->setExpectedException('\Vivo\Module\Exception\InvalidArgumentException');
        $factory    = new ModuleManagerFactory(array(), '');
    }

    public function testGetVmoduleManager()
    {
        $vModules   = array('vmod1', 'vmod2', 'vmod3');
        $vModuleManager = $this->factory->getModuleManager($vModules);
        $this->assertInstanceOf('\Zend\ModuleManager\ModuleManager', $vModuleManager);
    }
}
