<?php
namespace VivoTest\Storage;

use Vivo\Storage\StorageInterface;
use Vivo\Storage\LocalFileSystemStorage;

use PHPUnit_Framework_TestCase as TestCase;

class StorageUtilTest extends TestCase
{
    const STORAGE_SUBDIR_1  = 'TestDir1';
    const STORAGE_SUBDIR_2  = 'TestDir2';

    /**
     * @var StorageInterface
     */
    protected $storage1;

    /**
     * @var StorageInterface
     */
    protected $storage2;

    /**
     * @var string
     */
    protected $sysTmp;

    protected function setUp()
    {
        $this->sysTmp = sys_get_temp_dir();
        $this->cleanUp($this->sysTmp);
        $storageRoot1   = $this->sysTmp . DIRECTORY_SEPARATOR . self::STORAGE_SUBDIR_1;
        $storageRoot2   = $this->sysTmp . DIRECTORY_SEPARATOR . self::STORAGE_SUBDIR_2;
        mkdir($storageRoot1);
        mkdir($storageRoot2);
        $this->storage1 = new LocalFileSystemStorage(array('root' => $storageRoot1));
        $this->storage2 = new LocalFileSystemStorage(array('root' => $storageRoot2));
    }

    protected function tearDown()
    {
        $this->cleanUp($this->sysTmp);
    }

    protected function cleanUp($sysTmp)
    {
        $this->rrmdir($sysTmp . DIRECTORY_SEPARATOR . self::STORAGE_SUBDIR_1);
        $this->rrmdir($sysTmp . DIRECTORY_SEPARATOR . self::STORAGE_SUBDIR_2);
    }

    /**
     * Recursively removes a directory in file system
     * @param string $dir
     */
    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $path   = $dir . DIRECTORY_SEPARATOR . $object;
                    if (is_dir($path)) {
                        rrmdir($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function testCopy()
    {
        //TODO - implement testCopy()
        $this->assertTrue(true);
    }
}

