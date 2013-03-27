<?php
namespace VivoTest\Module;

use Vivo\Module\StreamWrapper;
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
        $this->setExpectedException('\Vivo\Module\Exception\StreamException');
        StreamWrapper::register('', $this->storage);
    }
}
