<?php
namespace VivoTest\Storage;

use Vivo\Storage\LocalFileSystemStorage;
use Vivo\Storage\PathBuilder\PathBuilderInterface;

/**
 * Local file system storage test case.
 */
class LocalFileSystemStorageTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var string
	 */
	private $temp;

    /**
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

	/**
	 * @var \Vivo\Storage\LocalFileSystemStorage
	 */
	private $storage;

	protected function setUp() {
		$this->temp = sys_get_temp_dir();
        $this->pathBuilder  = new \Vivo\Storage\PathBuilder\PathBuilder('/');
		$this->storage = new LocalFileSystemStorage(array('root'=>$this->temp, 'path_builder' => $this->pathBuilder));
	}

	/**
	 * @expectedException \Vivo\Storage\Exception\InvalidArgumentException
	 */
	public function testConstructRootNotDefined() {
		$storage = new LocalFileSystemStorage(array('foo'=>$this->temp));
	}

	/**
	 * @expectedException \Vivo\Storage\Exception\InvalidArgumentException
	 */
	public function testConstructRootIsNotDirectory() {
		$storage = new LocalFileSystemStorage(array('root'=>$this->temp.'/'.time()));
	}

	public function testSet() {
		$path = '/testSet/file';
		$file = $this->temp.$path;
		$data = __METHOD__;

		$this->storage->set($path, $data);

		$this->assertEquals($data, file_get_contents($file), "Set data '$data'");

		unlink($file);
		rmdir($this->temp.DIRECTORY_SEPARATOR.'testSet');
	}

	public function testGet() {
		$path = '/testGet/file';
		$dir = $this->temp.DIRECTORY_SEPARATOR.'testGet';
		$file = $this->temp.$path;
		$data = __METHOD__;

		mkdir($dir);
		file_put_contents($file, $data);

		$this->assertEquals($data, $this->storage->get($path), "Get data '$data'");

		unlink($file);
		rmdir($dir);
	}

	/**
	 * @expectedException \Vivo\Storage\Exception\IOException
	 */
	public function testGetFileNotFound() {
		$this->storage->get('/FileNotFoundPath/file');
	}

	public function testContains() {
		$path = '/testContains';
		$file = $path.'/file';

		mkdir($this->temp.$path);
		file_put_contents($this->temp.$file, __METHOD__);

		$this->assertTrue($this->storage->contains($file), 'Object not found');

		unlink($this->temp.$file);
		rmdir($this->temp.$path);
	}

	public function testIsObject() {
		$dir = '/testIsObject';
		$file = $dir.'/file';

		mkdir($this->temp.$dir);
		file_put_contents($this->temp.$file, __METHOD__);

		$this->assertTrue($this->storage->isObject($file), "File '$file' is not a object");
		$this->assertFalse($this->storage->isObject($dir), "Directory '$dir' is a object");

		unlink($this->temp.$file);
		rmdir($this->temp.$dir);
	}

	public function testMtime() {
		$path = '/testMtime';
		$file = $this->temp.$path;

		file_put_contents($file, __METHOD__);

		$mtime1 = filemtime($file);
		$mtime2 = $this->storage->mtime($path);

		$this->assertSame($mtime1, $mtime2);

		unlink($file);
	}

	public function testTouch() {
		$path = '/testTouch';
		$file = $this->temp.$path;
		$sleep = 1;

		file_put_contents($file, __METHOD__);
		$mtime1 = filemtime($file);

		sleep($sleep);

		$this->storage->touch($path);
		$mtime2 = filemtime($file);

		$this->assertNotEquals($mtime1, $mtime2, 'File mtimes are same');
		$this->assertEquals($mtime1 + $sleep, $mtime2);

		unlink($file);
	}

	/**
	 * Move.
	 *
	 * /testMove/dir1/dir2/file2
	 * /testMove/dir1/file1
	 * /testMove/dir3
	 *
	 * /testMove/dir1 move to /testMove/dir3
	 *
	 * /testMove/dir3/dir2/file2
	 * /testMove/dir3/file1
	 */
	public function testMove() {
		$dir = $this->temp.'/testMove';
        if (is_dir($dir)) {
            $this->rrmdir($dir);
        }
		mkdir($dir);
		mkdir($dir.'/dir1');
		mkdir($dir.'/dir1/dir2');
		mkdir($dir.'/dir3');
		file_put_contents($dir.'/dir1/dir2/file2', __METHOD__);
		file_put_contents($dir.'/dir1/file1', __METHOD__);

		$success = $this->storage->move('/testMove/dir1', '/testMove/dir3');

		$this->assertTrue($success, 'Move returns FALSE');

		$this->assertTrue(is_dir($dir.'/dir3/dir2'));
		$this->assertTrue(is_file($dir.'/dir3/file1'));
		$this->assertFalse(is_dir($dir.'/dir3/dir2/file2'));

		unlink($dir.'/dir3/dir2/file2');
		unlink($dir.'/dir3/file1');
		rmdir($dir.'/dir3/dir2');
		rmdir($dir.'/dir3');
		rmdir($dir);
	}

	public function testCopy() {

	}

	public function testScan() {

	}

	public function testRemove() {

	}

	/**
	 * Only absolute paths supported test.
	 *
	 * @expectedException \Vivo\Storage\Exception\InvalidArgumentException
	 */
	public function testReadAbsolutePathsSupported() {
		$this->storage->read(sprintf('temp_%s', time()));
	}

    public function testWrite()
    {
        $sysTmp     = sys_get_temp_dir();
        $testDir    = $sysTmp . DIRECTORY_SEPARATOR . 'TestDir';
        $this->rrmdir($testDir);
        mkdir($testDir, 0777, true);
        $file       = '/a/b/c/file.txt';
        $components = array('a', 'b', 'c', 'file.txt');
        array_pop($components);
        $storage    = new LocalFileSystemStorage(array('root' => $testDir, 'path_builder' => $this->pathBuilder));
        $output     = $storage->write($file);
        $data       = 'foo bar baz';
        $bytes      = $output->write($data);
        $this->assertEquals(strlen($data), $bytes);
        $this->assertEquals($data, $storage->get($file));
        //Permission denied?
        //$this->rrmdir($testDir);
    }

    public function testSize()
    {
        $storagePath    = '/sizeTest.txt';
        $fileName       = $this->temp . $storagePath;
        $contents       = 'Lorem ipsum dolor sit amet.';
        $written        = file_put_contents($fileName, $contents);
        $this->assertGreaterThan(0, $written);
        $this->assertEquals($written, $this->storage->size($storagePath));
    }

    public function testGetPathBuilder()
    {
        $this->assertSame($this->pathBuilder, $this->storage->getPathBuilder());
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
                        $this->rrmdir($path);
                    } else {
                        unlink($path);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
