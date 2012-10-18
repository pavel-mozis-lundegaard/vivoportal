<?php
namespace VivoTest\Vmodule;

use Vivo\Vmodule\StreamWrapper;
use Vivo\Storage\StorageInterface;

/**
 * StreamWrapperTest
 */
class StreamWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    protected function setUp()
    {
        $this->storage  = $this->getMock('\Vivo\Storage\StorageInterface', array(), array(), '', false);
    }

    public function testRegisterExceptionStreamNameEmpty()
    {
        $this->setExpectedException('\Vivo\Vmodule\Exception\StreamException');
        StreamWrapper::register('', $this->storage);
    }
}
