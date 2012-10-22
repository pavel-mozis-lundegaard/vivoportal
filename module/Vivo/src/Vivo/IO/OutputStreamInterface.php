<?php
namespace Vivo\IO;

/**
 * @author kormik
 *
 */
interface OutputStreamInterface {

	/**
	 * Writes to stream
	 * @param string $bytes
	 */
	public function write($data);

}
