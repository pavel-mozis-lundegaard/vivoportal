<?php
namespace VivoTest\Storage;

use Vivo\Storage\Factory as StorageFactory;
use Vivo\Storage\LocalFs;

/**
 * FactoryTest
 * Storage factory test
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StorageFactory
     */
    protected $factory;

    protected function setUp()
    {
        $this->factory  = new StorageFactory();
    }

    public function testCreateExceptionOnNonArrayParam()
    {
        $this->setExpectedException('\Vivo\Storage\Exception\InvalidArgumentException');
        $this->factory->create('foo');
    }

    public function testCreateExceptionOnMissingClassParam()
    {
        $this->setExpectedException('\Vivo\Storage\Exception\InvalidArgumentException');
        $config = array(
            'foo'       => 'bar',
            'options'   => array('baz' => 'bat'),
        );
        $this->factory->create($config);
    }

    public function testCreateLocalFs()
    {
        $class  = '\Vivo\Storage\LocalFs';
        $config = array(
            'class'     => $class,
            'options'   => array('root' => __DIR__),
        );
        $storage    = $this->factory->create($config);
        $this->assertInstanceOf($class, $storage);
    }
}