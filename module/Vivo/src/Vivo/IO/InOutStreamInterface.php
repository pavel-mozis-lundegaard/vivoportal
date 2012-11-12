<?php
namespace Vivo\IO;

/**
 * InOutStreamInterface
 */
interface InOutStreamInterface extends InputStreamInterface, OutputStreamInterface
{
    /**
     * Sets the file position indicator and advances the file pointer.
     * The new position, measured in bytes from the beginning of the file,
     * is obtained by adding offset to the position specified by whence,
     * whose values are defined as follows:
     * SEEK_SET - Set position equal to offset bytes.
     * SEEK_CUR - Set position to current location plus offset.
     * SEEK_END - Set position to end-of-file plus offset. (To move to
     * a position before the end-of-file, you need to pass a negative value
     * in offset.)
     * SEEK_CUR is the only supported offset type for compound files
     *
     * Upon success, returns 0; otherwise, returns -1
     *
     * @param integer $offset
     * @param integer $whence
     * @return integer
     */
    public function seek($offset, $whence = SEEK_SET);

    /**
     * Get file position.
     * @return integer
     */
    public function tell();

    /**
     * Flush output.
     * Returns true on success or false on failure.
     * @return boolean
     */
    public function streamFlush();

    /**
     * Writes to stream
     * @param string $data
     * @param null $length
     * @return integer
     */
    public function write($data, $length = null);

}