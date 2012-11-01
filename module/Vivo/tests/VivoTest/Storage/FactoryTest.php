<?php
namespace VivoTest\Storage;

use Vivo\Storage\Factory as StorageFactory;

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
        $pathBuilder  = $this->getMock('Vivo\Storage\PathBuilder\PathBuilderInterface', array(), array(), '', false);
        $class  = '\Vivo\Storage\LocalFileSystemStorage';
        $config = array(
            'class'     => $class,
            'options'   => array('root' => __DIR__, 'path_builder' => $pathBuilder),
        );
        $storage    = $this->factory->create($config);
        $this->assertInstanceOf($class, $storage);
    }
}