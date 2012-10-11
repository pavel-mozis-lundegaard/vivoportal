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
	 */
	public function __construct($filename, $append = false) {
		$mode = $append ? 'a' : 'w';
		$this->fp = fopen($filename, $mode);
		if (!$this->fp) {
			throw new RuntimeException("Can not create stream for '$filename'");
		}
	}

	/**
	 * @param integer $bytes
	 * @return string
	 */
	public function write($data) {
		return fwrite($this->fp, $data);
	}

	/**
	 * Closes stream
	 */
	public function close() {
		fclose($this->ftp);
	}
}
