<?php
namespace VivoTest\IO;

use Vivo\IO\ByteArrayInputStream;
use Vivo\IO\FileInputStream;

/**
 * BufferedInputStream test case.
 */
class ByteArrayInputStreamTest extends \PHPUnit_Framework_TestCase {

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
		$this->stream = new ByteArrayInputStream($this->data);
	}
	
	public function testRead() {
		$data  = $this->stream->read(strlen($this->data));
		$this->assertEquals($this->data, $data);
	}
	
	public function testReadEmptyData() {
		$this->stream->read(strlen($this->data));
		$data  = $this->stream->read();
		$this->assertFalse($data);
	}
	
	/**
	 * @expectedException \Vivo\IO\Exception\InvalidArgumentException
	 */
	public function testReadWithBadParams() {
		$this->stream->read(-1);
	}
}
