<?php
namespace VivoTest\Module;

use Vivo\Module\ModuleManagerFactory;

use Zend\EventManager\EventManagerInterface;

/**
 * ModuleManagerFactoryTest
 */
class ModuleManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModuleManagerFactory
     */
    protected $factory;

    /**
     * @var EventManagerInterface
     */
    protected $appEvents;

    protected function setUp()
    {
        $vModulePaths   = array('/abc', '/def/ghi');
        $streamName     = 'vm';
        $this->appEvents    = $this->getMock('Zend\EventManager\EventManagerInterface', array(), array(), '', false);
        $this->factory  = new ModuleManagerFactory($vModulePaths, $streamName, $this->appEvents);
    }

    public function testConstructExceptionOnMissingStreamName()
    {
        $this->setExpectedException('\Vivo\Module\Exception\InvalidArgumentException');
        $factory    = new ModuleManagerFactory(array(), '', $this->appEvents);
    }

    public function testGetVmoduleManager()
    {
        $vModules   = array('vmod1', 'vmod2', 'vmod3');
        $vModuleManager = $this->factory->getModuleManager($vModules);
        $this->assertInstanceOf('\Zend\ModuleManager\ModuleManager', $vModuleManager);
    }
}
