<?php
namespace VivoTest\IO;

use Vivo\IO\IOUtil;

/**
 * BufferedInputStream test case.
 */
class IOUtilTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var Vivo\IO\InputStreamInterface
	 */
	private $source;
	
	/**
	 * @var Vivo\IO\InputStreamInterface
	 */
	private $target;
	
	/**
	 * @var string
	 */
	private $data = "Sample file content";

	/**
	 * Test setup 
	 */
	protected function setUp() {
		$this->source = $this->getMock('Vivo\IO\InputStreamInterface', array(), array(), '', false);
		$this->target = $this->getMock('Vivo\IO\OutputStreamInterface', array(), array(), '', false);
		$this->util = new IOUtil();
	}
	
	public function testCopy() {
		$bufferSize = 3;
		
		$this->source->expects($this->exactly(4))
		->method('read')
		->with($this->equalTo($bufferSize))
		->will($this->onConsecutiveCalls('abc','def', 'g', false));
		
		$this->target->expects($this->at(0))
		->method('write')
		->with($this->equalTo('abc'));

		$this->target->expects($this->at(1))
		->method('write')
		->with($this->equalTo('def'));
		
		$this->target->expects($this->at(2))
		->method('write')
		->with($this->equalTo('g'));
		
		$copied = $this->util->copy($this->source, $this->target, $bufferSize);
		
		$this->assertEquals(7, $copied);
		
	}
}
