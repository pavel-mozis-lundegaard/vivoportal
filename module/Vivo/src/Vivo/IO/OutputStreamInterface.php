<?php
namespace Vivo\IO;

/**
 * @author kormik
 *
 */
interface OutputStreamInterface {

	/**
	 * Writes to stream
	 * @param string $data
     * @return integer
	 */
	public function write($data);

}
