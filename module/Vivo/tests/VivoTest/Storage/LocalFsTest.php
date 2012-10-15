<?php
namespace VivoTest\Storage;

use Vivo\Storage\LocalFs;

/**
 * Local file system storage.
 */
class LocalFsTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var string
	 */
	private $temp;
	/**
	 * @var Vivo\Storage\LocalFs
	 */
	private $storage;

	protected function setUp() {
		$this->temp = sys_get_temp_dir();
		$this->storage = new LocalFs($this->temp);
	}

	public function testSet() {
		$path = DIRECTORY_SEPARATOR.'testSet'.DIRECTORY_SEPARATOR.'file';
		$file = $this->temp.$path;
		$data = __METHOD__;

		$this->storage->set($path, $data);

		$this->assertEquals($data, file_get_contents($file), "Set data '$data'");

		unlink($file);
		rmdir($this->temp.DIRECTORY_SEPARATOR.'testSet');
	}

	public function testGet() {
		$path = DIRECTORY_SEPARATOR.'testGet'.DIRECTORY_SEPARATOR.'file';
		$dir = $this->temp.DIRECTORY_SEPARATOR.'testGet';
		$file = $this->temp.$path;
		$data = __METHOD__;

		mkdir($dir);
		file_put_contents($file, $data);

		$this->assertEquals($data, $this->storage->get($path), "Get data '$data'");

		unlink($file);
		rmdir($dir);
	}

	public function testContains() {
		$path = DIRECTORY_SEPARATOR.'testContains';
		$file = $path.DIRECTORY_SEPARATOR.'file';

		mkdir($this->temp.$path);
		file_put_contents($this->temp.$file, __METHOD__);

		$this->assertTrue($this->storage->contains($file), 'Object not found');

		unlink($this->temp.$file);
		rmdir($this->temp.$path);
	}

	public function testIsObject() {
		$dir = DIRECTORY_SEPARATOR.'testIsObject';
		$file = $dir.DIRECTORY_SEPARATOR.'file';

		mkdir($this->temp.$dir);
		file_put_contents($this->temp.$file, __METHOD__);

		$this->assertTrue($this->storage->isObject($file), "File '$file' is not a object");
		$this->assertFalse($this->storage->isObject($dir), "Directory '$dir' is a object");

		unlink($this->temp.$file);
		rmdir($this->temp.$dir);
	}

}
