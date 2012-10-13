<?php
namespace Vivo\Storage;

/**
 * StorageInterface
 */
interface StorageInterface {

	/**
	 * Checks whether item exists
	 * @param string $path to item
	 * @return boolean TRUE if item exists otherwise FALSE
	 */
	public function contains($path);

	/**
	 * Checks whether item on the given path is an object.
	 * @param string $path Path to the item
	 * @return bool
	 */
	public function isObject($path);

	/**
	 * Returns item modification time in milliseconds.
	 * @param string $path to item
	 * @return int|bool Item modification time in milliseconds or FALSE if the item doesn't exist
	 */
	public function mtime($path);

	/**
	 * Retrieves an item from storage and returns it
	 * If the item doesn't exist under specified path returns NULL
	 * @param string $path to item
	 * @return mixed|null
	 */
	public function get($path);

	/**
	 * Saves an item to storage
	 * If an item already exists under the specified path, it will be overwritten
	 * @param string $path to item
	 * @param mixed $data
	 */
	public function set($path, $data);

	/**
	 * Touches item in the storage
	 * Resets the item modification time to the current system time (equivalent to the standard UNIX command touch)
	 * @param string $path to item
	 */
	public function touch($path);

	/**
	 * Renames/moves an item (equivalent to the standard UNIX command cp (copy))
	 * @param string $path to item
	 * @param string $target path
	 */
	public function move($path, $target);

	/**
	 * Copies an item to another location (equivalent to standard UNIX command mv (move))
	 * @param string $path to item
	 * @param string $target path
	 */
	public function copy($path, $target);

	/**
	 * Returns list of child items
	 * @param string $path to item
	 */
	public function scan($path);

	/**
	 * Removes an item from specified path and returns true
     * If the item doesn't exist, returns false
	 * @param string $path to item
	 * @return boolean TRUE on success, FALSE if the item doesn't exist
	 */
	public function remove($path);
}
