<?php
namespace Vivo\IO;

/**
 * @author kormik
 *
 */
interface InputStreamInterface {

	/**
	 * Reads from stream.
	 * @param integer $bytes
	 */
	public function read($bytes = 1);

}
