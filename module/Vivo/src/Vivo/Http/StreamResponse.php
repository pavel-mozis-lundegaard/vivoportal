<?php
namespace Vivo\Stream;

use Vivo\IO\InputStreamInterface;
use Vivo\IO\FileOutputStream;
use Vivo\IO\Util;

use Zend\Http\PhpEnvironment\Response as PHPResponse;

/**
 * 
 * Response object that supports setting stream as content.
 * @author kormik
 *
 */
class StreamResponse extends PHPResponse {

	/**
	 * @var \Vivo\IO\InputStreamInterface
	 */
	private $stream;
	
	/**
	 * @param InputStreamInterface $stream
	 */
	public function setStream(InputStreamInterface $stream) {
		$this->stream = $stream;
	}
	
	/**
	 * @return \Vivo\IO\InputStreamInterface
	 */
	public function getStream() {
		return $this->stream;
	}
	
	/* (non-PHPdoc)
	 * @see Zend\Http\PhpEnvironment.Response::sendContent()
	 */
	public function sendContent() {
			if ($this->contentSent()) {
				return $this;
			}
			
			if (!$source = $this->getStream()) {
				return parent::sendContent();
			}

			$target = new FileOutputStream('php://output');
			$util = new Util();
			$util->copy($source, $target);
			$target->close();
			$this->contentSent = true;
			return $this;
	}
}
