<?php
namespace VivoTest\Storage\StorageCache;

use Vivo\Storage\StorageInterface;
use Vivo\Storage\StorageCache\StorageCache;
use Vivo\IO;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Cache\Storage\StorageInterface as ZendCache;

/**
 * StorageCacheTest
 */
class StorageCacheTest extends TestCase
{
    /**
     * @var ZendCache
     */
    protected $cache;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var StorageCache
     */
    protected $storageCache;

    protected function setUp()
    {
        $mockedMethodsCache = array('setItem', 'hasItem', 'removeItem');
        $this->cache        = $this->getMock('VivoTest\SharedTestClasses\FsCacheMock',
                                             $mockedMethodsCache, array(), '', false);
        $this->storage      = $this->getMock('Vivo\Storage\StorageInterface', array(), array(), '', false);
        $this->storageCache = new StorageCache($this->cache, $this->storage);
    }

    public function testSetCallsSetOnCacheAndStorage()
    {
        $path   = 'foo/bar';
        $data   = 'baz';
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($path), $this->equalTo($data));
        $this->storage->expects($this->once())
            ->method('set')
            ->with($this->equalTo($path), $this->equalTo($data));
        $this->storageCache->set($path, $data);
    }

    public function testGetRetrievesFirstFromCache()
    {
        $path   = 'foo/bar';
        $data   = 'baz';
        $this->cache->setData($data);
        $this->cache->setSuccess(true);
        $this->storage->expects($this->never())
            ->method('get');
        $dataRead   = $this->storageCache->get($path);
        $this->assertEquals($data, $dataRead);
    }

    public function testGetRetrievesFromStorageIfNotInCache()
    {
        $path   = 'foo/bar';
        $data   = 'baz';
        //Set up the cache mock not to find the data
        $this->cache->setData(null);
        $this->cache->setSuccess(false);
        //We expect the storage to be called to retrieve the item
        $this->storage->expects($this->once())
            ->method('get')
            ->with($this->equalTo($path))
            ->will($this->returnValue($data));
        //We expect the cache to get called to store the newly read data from storage
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($path), $this->equalTo($data));
        $dataRead   = $this->storageCache->get($path);
        $this->assertEquals($data, $dataRead);
    }

    public function testGetCacheIsNotCalledToStoreForNotFoundItem()
    {
        $path   = 'foo/bar';
        //Set up the cache mock not to find the data
        $this->cache->setData(null);
        $this->cache->setSuccess(false);
        //We expect the storage to be called to retrieve the item
        $this->storage->expects($this->once())
            ->method('get')
            ->with($this->equalTo($path))
            ->will($this->returnValue(null));
        //We expect the cache not to be called
        $this->cache->expects($this->never())
            ->method('setItem');
        $dataRead   = $this->storageCache->get($path);
        $this->assertNull($dataRead);
    }

    public function testContainsDoesNotQueryStorageIfInCache()
    {
        $path   = 'foo/bar';
        $this->cache->expects($this->once())
            ->method('hasItem')
            ->with($this->equalTo($path))
            ->will($this->returnValue(true));
        $this->storage->expects($this->never())
            ->method('contains');
        $this->assertTrue($this->storageCache->contains($path));
    }

    public function testContainsQueriesStorageIfNotInCache()
    {
        $path   = 'foo/bar';
        $this->cache->expects($this->once())
            ->method('hasItem')
            ->with($this->equalTo($path))
            ->will($this->returnValue(false));
        $this->storage->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($path))
            ->will($this->returnValue(true));
        $this->assertTrue($this->storageCache->contains($path));
    }

    public function testMoveWhenNotFoundInCache()
    {
        $path   = 'foo/bar';
        $target = 'baz/bat';
        //Set-up the cache mock not to find the item
        $this->cache->setSuccess(false);
        $this->cache->expects($this->never())
            ->method('removeItem');
        $this->cache->expects($this->never())
            ->method('setItem');
        $this->storage->expects($this->once())
            ->method('move')
            ->with($this->equalTo($path), $this->equalTo($target));
        $this->storageCache->move($path, $target);
    }

    public function testMoveWhenFoundInCache()
    {
        $path   = 'foo/bar';
        $target = 'baz/bat';
        $data   = 'qux';
        //Set-up the cache mock to find the item
        $this->cache->setSuccess(true);
        $this->cache->setData($data);
        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($path));
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($target), $this->equalTo($data));
        $this->storage->expects($this->once())
            ->method('move')
            ->with($this->equalTo($path), $this->equalTo($target));
        $this->storageCache->move($path, $target);
    }

    public function testCopyWhenNotFoundInCache()
    {
        $path   = 'foo/bar';
        $target = 'baz/bat';
        //Set-up the cache mock not to find the item
        $this->cache->setSuccess(false);
        $this->cache->expects($this->never())
            ->method('setItem');
        $this->storage->expects($this->once())
            ->method('copy')
            ->with($this->equalTo($path), $this->equalTo($target));
        $this->storageCache->copy($path, $target);
    }

    public function testCopyWhenFoundInCache()
    {
        $path   = 'foo/bar';
        $target = 'baz/bat';
        $data   = 'qux';
        //Set-up the cache mock to find the item
        $this->cache->setSuccess(true);
        $this->cache->setData($data);
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($target), $this->equalTo($data));
        $this->storage->expects($this->once())
            ->method('copy')
            ->with($this->equalTo($path), $this->equalTo($target));
        $this->storageCache->copy($path, $target);
    }

    public function testRemove()
    {
        $path   = 'foo/bar';
        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($path));
        $this->storage->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($path));
        $this->storageCache->remove($path);
    }

    public function testScan()
    {
        $path   = 'foo/bar';
        $this->storage->expects($this->once())
            ->method('scan')
            ->with($this->equalTo($path));
        $this->storageCache->scan($path);
    }

    public function testTouch()
    {
        $path   = 'foo/bar';
        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($path));
        $this->storage->expects($this->once())
            ->method('touch')
            ->with($this->equalTo($path));
        $this->storageCache->touch($path);
    }

    public function testRead()
    {
        $inputStream  = $this->getMock('Vivo\IO\InputStreamInterface', array(), array(), '', false);
        $path   = 'foo/bar';
        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($path));
        $this->storage->expects($this->once())
            ->method('read')
            ->with($this->equalTo($path))
            ->will($this->returnValue($inputStream));
        $stream = $this->storageCache->read($path);
        $this->assertInstanceOf('\Vivo\IO\InputStreamInterface', $stream);
    }

    public function testWrite()
    {
        $outputStream  = $this->getMock('Vivo\IO\OutputStreamInterface', array(), array(), '', false);
        $path   = 'foo/bar';
        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($path));
        $this->storage->expects($this->once())
            ->method('write')
            ->with($this->equalTo($path))
            ->will($this->returnValue($outputStream));
        $stream = $this->storageCache->write($path);
        $this->assertInstanceOf('\Vivo\IO\OutputStreamInterface', $stream);
    }
}