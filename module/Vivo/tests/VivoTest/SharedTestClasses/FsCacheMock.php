<?php
namespace VivoTest\SharedTestClasses;

use Zend\Cache\Storage\Adapter\Filesystem as FsCache;
use Vivo\IO;

/**
 * FsCacheMock
 * Implemented to enable mocking of returned values acquired via parameters passed by reference
 */
class FsCacheMock extends FsCache
{
    /**
     * Data to be returned by getItem()
     * @var mixed
     */
    protected $data;

    /**
     * Has the getItem() call been successful?
     * @var boolean
     */
    protected $success;

    /**
     * Get an item.
     * This method cannot be mocked using PHPUnit's getMock(), because it needs to set a parameter passed by reference
     *
     * @param  string  $key
     * @param  boolean $success
     * @param  mixed   $casToken
     * @return mixed Data on success, null on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        $success    = $this->success;
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }
}
