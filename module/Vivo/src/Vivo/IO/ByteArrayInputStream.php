<?php
namespace Vivo\IO;

use Vivo\IO\InputStreamInterface;

/**
 * @author kormik
 *
 */
class ByteArrayInputStream implements InputStreamInterface {

	/**
	 * @var integer
	 */
	private $position = 0;
	
	/**
	 * @var string
	 */
	private $data;

	/**
	 * 
	 * @param string $data
	 */
	public function __construct(&$data) {
		$this->data =& $data;
	}

	/**
	 * @param int $bytes
	 * @return string
	 */
	public function read($bytes = 1) {
		$data = substr($this->data, $this->position, $bytes);
		$this->position += $bytes;
		return $data;
	}
	
	public function close() {
		//nothing to do
	}
}
