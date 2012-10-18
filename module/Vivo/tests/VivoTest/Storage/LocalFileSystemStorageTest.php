<?php
namespace VivoTest\Storage;

use Vivo\Storage\LocalFileSystemStorage;

/**
 * Local file system storage test case.
 */
class LocalFileSystemStorageTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var string
	 */
	private $temp;
	/**
	 * @var Vivo\Storage\LocalFileSystemStorage
	 */
	private $storage;

	protected function setUp() {
		$this->temp = sys_get_temp_dir();
		$this->storage = new LocalFileSystemStorage(array('root'=>$this->temp));
	}

	public function testConstructRootNotDefined() {
		$this->setExpectedException('Vivo\Storage\Exception\InvalidArgumentException');
		$storage = new LocalFileSystemStorage(array('foo'=>$this->temp));
	}

	public function testConstructRootIsNotDirectory() {
		$this->setExpectedException('Vivo\Storage\Exception\InvalidArgumentException');
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

	public function testGetFileNotFound() {
		$this->setExpectedException('Vivo\Storage\Exception\IOException');
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

}
