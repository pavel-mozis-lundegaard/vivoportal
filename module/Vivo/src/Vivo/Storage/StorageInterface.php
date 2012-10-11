<?php
namespace Vivo\Storage;

interface StorageInterface {

	/**
	 * Checks whether item exists and (optionally) has not expired yet.
	 * Expiration parameter is used typically when virtual file system is used to implement a cache mechanism.
	 * @param string path to item
	 * @return boolean TRUE if item exists and optionally if has not expired yet; otherwise FALSE
	 */
	public function contains($path);

	/**
	 * Checks whether item on the given path is a object.
	 * @param string $path Path to the item
	 * @return bool
	 */
	public function isObject();

	/**
	 * Returns item modification time in milliseconds.
	 * @param string path to item
	 * @return mixed item modification time in milliseconds or FALSE if item doesn't exist
	 */
	public function mtime($path);

	/**
	 * Returns and from file system.
	 * If an item doesn't exist under specified path, NULL value will be returned.
	 * @param string path to item
	 * @return mixed|null
	 */
	public function get($path);

	/**
	 * Saves item to file system.
	 * If and item under specified path already exists, it will be overwritten.
	 * @param string path to item
	 * @param mixed item data
	 */
	public function set($path, $variable);

	/**
	 * Touches item in file system.
	 * Touching means resetting an item modification time to the current system time. The public function of this method is equivalent to standard UNIX command touch.
	 * @param path to item
	 */
	public function touch($path);

	/**
	 * Renames/moves item.
	 * The public function of this method is equivalent to standard UNIX command cp (copy).
	 * @param string path to item
	 * @param string target path
	 */
	public function move($path, $target);

	/**
	 * Copies item to another location.
	 * The public function of this method is equivalent to standard UNIX command mv (move).
	 * @param string path to item
	 * @param string path to copy
	 */
	public function copy($path, $target);

	/**
	 * Returns list of child items.
	 * @param string path to item
	 */
	public function scan($path);

	/**
	 * Removes item from specified path. If the item doesn't exist, nothing happens.
	 * nedělá nic.
	 * @param path to item
	 * @return boolean TRUE on success, FALSE if item doesn't exist
	 */
	public function remove($path);
	
	/**
	 * Returns input stream for reading resource.
	 * @param string $path
	 * @return \Vivo\IO\InputStreamInterface
	 */
	public function read($path);
	
	/**
	 * Returns output stream for writing resource. 
	 * @param string $path
	 * @return \Vivo\IO\OutputStreamInterface
	 */
	public function write($path);
}
