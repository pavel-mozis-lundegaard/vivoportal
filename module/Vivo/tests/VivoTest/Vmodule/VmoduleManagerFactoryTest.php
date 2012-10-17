<?php
namespace VivoTest\Vmodule;

use Vivo\Vmodule\VmoduleManagerFactory;

/**
 * VmoduleManagerFactoryTest
 */
class VmoduleManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var VmoduleManagerFactory
     */
    protected $factory;

    protected function setUp()
    {
        $vModulePaths   = array('/abc', '/def/ghi');
        $streamName     = 'vm';
        $this->factory  = new VmoduleManagerFactory($vModulePaths, $streamName);
    }

    public function testConstructExceptionOnMissingStreamName()
    {
        $this->setExpectedException('\Vivo\Vmodule\Exception\InvalidArgumentException');
        $factory    = new VmoduleManagerFactory(array(), '');
    }

    public function testGetVmoduleManager()
    {
        $vModules   = array('vmod1', 'vmod2', 'vmod3');
        $vModuleManager = $this->factory->getVmoduleManager($vModules);
        $this->assertInstanceOf('\Zend\ModuleManager\ModuleManager', $vModuleManager);
    }
}
