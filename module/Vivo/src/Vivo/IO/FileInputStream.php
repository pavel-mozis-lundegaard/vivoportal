<?php
namespace Vivo\IO;

use Vivo\IO\Exception\RuntimeException;
use Vivo\IO\Exception\InvalidArgumentException;

/**
 * @author kormik
 *
 */
class FileInputStream implements InputStreamInterface, CloseableInterface {

	/**
	 * @var resource
	 */
	private $fp;
	
	/**
	 * @var boolean
	 */
	private $closed = false;
	
	/**
	 * @param string $file
	 * @throws RuntimeException
	 */
	public function __construct($filename) {
		$this->fp = fopen($filename, 'r');
		if (!$this->fp) {
			throw new RuntimeException("Can not create stream for '$filename'");
		}
	}

	/**
	 * Reads data from stream.
	 * @param integer $bytes
	 * @return string
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function read($bytes = 1) {
		if (!is_int($bytes) || $bytes < 1) {
			throw new InvalidArgumentException('Parameter $bytes must be integer.');
		}
		
		if ($this->isClosed()) {
			throw new RuntimeException('Can not read from closed stream.');			
		}
		return fread($this->fp, $bytes)?:false;
	}

	/**
	 * Closes stream.
	 */
	public function close() {
		if (!$this->isClosed()) {
			fclose($this->fp);
			$this->closed = true;
		}
	}
	
	/**
	 * @return boolean
	 */
	public function isClosed() {
		return $this->closed;		
	}
}
