<?php
namespace Vivo\IO;

use Vivo\IO\InputStreamInterface;

/**
 * @author kormik
 *
 */
class BufferedInputStream implements InputStreamInterface {

	/**
	 * @var integer
	 */
	private $position = 0;
	
	/**
	 * @var \Vivo\IO\InputStreamInterface
	 */

	private $is;
	
	/**
	 * @var integer
	 */
	private $bufferSize;
	
	/**
	 * @var string
	 */
	private $buffer = '';
	
	/**
	 * 
	 * @param string $data
	 */
	public function __construct(InputStreamInterface $is, $bufferSize = 1024) {
		$this->is = $is;
		$this->bufferSize = $bufferSize;
	}

	/**
	 * @param int $bytes
	 * @return string
	 */
	public function read($bytes = 1) {
		while (strlen($this->buffer) < $bytes && $this->loadBuffer()); 
		$data = substr($this->buffer, 0, $bytes);
		$this->buffer = substr($this->buffer, $bytes); 
		return $data?:false;
	}
	
	/**
	 * @return boolean
	 */
	private function loadBuffer() {
		$data =  $this->is->read($this->bufferSize);
		if ($data === false) {
			return false;
		}
		$this->buffer .= $data;
		return true; 
	}
	
	public function close() {
		$this->buffer = ''; //free memory
		$this->is->close();
	}
}
