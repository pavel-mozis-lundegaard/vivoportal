<?php
namespace VivoTest\IO;

use Vivo\IO\BufferedInputStream;

use Vivo\IO\FileInputStream;

/**
 * BufferedInputStream test case.
 */
class BufferedInputStreamTest extends \PHPUnit_Framework_TestCase {
	
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
		//$this->file = sys_get_temp_dir().self::filename;
		//file_put_contents($this->file, $this->data);
		$this->is = $this->getMock('Vivo\IO\InputStreamInterface', array(), array(), '', false);
	}
	
	protected function tearDown() {
		//$this->stream->close();
	}

	public function testReadLessThanBufferSize() {
		$bufferSize = 10;
		$returnedData = substr($this->data, 0, $bufferSize);
		$this->stream = new BufferedInputStream($this->is, $bufferSize);
				
		$this->is->expects($this->once())
			->method('read')
			->with($this->equalTo($bufferSize))
			->will($this->returnValue($returnedData));
		$data = $this->stream->read(5);
		
		$this->assertEquals(substr($this->data, 0,5), $data);
	}
	
	public function testReadMoreThanBufferSize() {
		$bufferSize = 2;
		$this->stream = new BufferedInputStream($this->is, $bufferSize);
		
		$this->is->expects($this->exactly(3))
		->method('read')
		->with($this->equalTo($bufferSize))
		->will($this->onConsecutiveCalls('ab', 'cd', 'ef', 'gh'));
		$data = $this->stream->read(5);
		
		$this->assertEquals('abcde', $data);
	}
	
	public function testReadWhenSourceStreamFinished() {
		$bufferSize = 2;
		$this->stream = new BufferedInputStream($this->is, $bufferSize);
		
		$this->is->expects($this->exactly(3))
		->method('read')
		->with($this->equalTo($bufferSize))
		->will($this->onConsecutiveCalls('ab', 'c', false));
		$data = $this->stream->read(10);
		$this->assertEquals('abc', $data);
	}
	
 	public function testReadWhenSourceStreamReturnFalse() {
 		$bufferSize = 2;
 		$this->stream = new BufferedInputStream($this->is, $bufferSize);
 		$this->is->expects($this->once())
	 		->method('read')
 			->with($this->equalTo($bufferSize))
 			->will($this->returnValue(false));
 		$data = $this->stream->read(10);
 		$this->assertFalse($data);
 	}
	
	/**
	 * @expectedException \Vivo\IO\Exception\InvalidArgumentException
	 */
	public function testReadWithBadParams() {
		$stream = new BufferedInputStream($this->is);
		$stream->read(-1);
	}
	
 	/**
 	 * @expectedException \Vivo\IO\Exception\RuntimeException
 	 */
 	public function testReadFromClosed() {
 		$stream = new BufferedInputStream($this->is);
 		$stream->close();
 		$stream->read();
 	}
	
}

class XX {
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
