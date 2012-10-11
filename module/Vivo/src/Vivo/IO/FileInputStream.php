<?php
namespace Vivo\IO;

use Vivo\IO\Exception\RuntimeException;

/**
 * @author kormik
 *
 */
class FileInputStream implements InputStreamInterface {

	/**
	 * @var resource
	 */
	private $fp;

	/**
	 * @param string $file
	 */
	public function __construct($filename) {
		$this->fp = fopen($filename, 'r');
		if (!$this->fp) {
			throw new RuntimeException("Can not create stream for '$filename'");
		}
	}

	/**
	 * @param integer $bytes
	 * @return string
	 */
	public function read($bytes = 1) {
		return fread($this->fp, $bytes)?:false;
	}

	/**
	 * Closes stream
	 */
	public function close() {
		fclose($this->ftp);
	}
}
