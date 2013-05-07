<?php
namespace Vivo\IO;

/**
 * @author kormik
 *
 */
interface OutputStreamInterface extends CloseableInterface {

	/**
	 * Writes to stream
	 * @param string $data
	 * @return integer
	 */
	public function write($data);

}
