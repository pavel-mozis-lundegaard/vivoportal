<?php
namespace VivoTest\IO;

use Vivo\IO\BufferedInputStream;

use Vivo\IO\FileInputStream;

/**
 * BufferedInputStream test case.
 */
class BufferedInputStreamTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Vivo\IO\FileInputStream
	 */
	private $stream;
	
	/**
	 * @var string
	 */
	private $data = "Sample file content";
	
	/**
	 * @var Vivo\IO\InputStreamInterface
	 */
	private $is;

	/**
	 * Test setup 
	 */
	protected function setUp() {
		$this->is = $this->getMock('Vivo\IO\InputStreamInterface', array(), array(), '', false);
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