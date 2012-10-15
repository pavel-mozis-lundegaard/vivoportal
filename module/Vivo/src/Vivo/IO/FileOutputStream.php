<?php
namespace Vivo\IO;

use Vivo\IO\Exception\RuntimeException;

/**
 * @author kormik
 *
 */
class FileOutputStream implements OutputStreamInterface {

	/**
	 * @var resource
	 */
	private $fp;
	
	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @param string $path
	 * @param boolean $append 
	 */
	public function __construct($filename, $append = false) {
		$mode = $append ? 'a' : 'w';
		$this->fp = fopen($filename, $mode);
		if (!$this->fp) {
			throw new RuntimeException("Can not create stream for '$filename'");
		}
	}

	/**
	 * Writes data to the stream.
	 * @param integer $bytes
	 * @return string
	 */
	public function write($data) {
		return fwrite($this->fp, $data);
	}

	/**
	 * Closes stream.
	 */
	public function close() {
		fclose($this->fp);
	}
}
