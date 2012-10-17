<?php
namespace VivoTest\IO;

use Vivo\IO\FileInputStream;

/**
 * FileInputStream test case.
 */
class FileInputStreamTest extends \PHPUnit_Framework_TestCase {
	
	const filename = 'test.txt';
	
	/**
	 * @var string
	 */
	private $file;
	/**
	 * @var Vivo\IO\FileInputStream
	 */
	private $stream;
	
	/**
	 * @var string
	 */
	private $data = "Sample file content";

	/**
	 * Test setup 
	 */
	protected function setUp() {
		$this->file = sys_get_temp_dir().self::filename;
		file_put_contents($this->file, $this->data);
		$this->stream = new FileInputStream($this->file);		

	}
	
	protected function tearDown() {
		$this->stream->close();
	} 

	public function testRead() {
		$data = $this->stream->read();
		$length = strlen($data);
		$this->assertEquals(1, $length, "Readed $length bytes");
		$this->assertEquals(substr($this->data,0,1), $data);
	}
	
	public function testReadMoreThanAvailable() {
		$data = $this->stream->read(strlen($this->data) + 1);
		$this->assertEquals(strlen($data), strlen($this->data));
		$this->assertEquals($data, $this->data);
	}
	
	public function testReadWhenNothingToRead() {
		$this->stream->read(strlen($this->data)); //read all data from stream
		$data = $this->stream->read();
		$this->assertFalse($data);
	}
	
	/**
	 * @expectedException \Vivo\IO\Exception\InvalidArgumentException
	 */
	public function testReadWithBadParam() {
		$this->stream->read(-1);
	}
	
	public function testClose() {
		$this->assertFalse($this->stream->isClosed());
		$this->stream->close();
		$this->assertTrue($this->stream->isClosed());
		$this->stream->close(); //close again
		$this->assertTrue($this->stream->isClosed());
	}
	
	/**
	 * @expectedException \Vivo\IO\Exception\RuntimeException
	 */
	public function testReadFromClosed() {
		$this->stream->close();
		$this->stream->read();
	}
}
