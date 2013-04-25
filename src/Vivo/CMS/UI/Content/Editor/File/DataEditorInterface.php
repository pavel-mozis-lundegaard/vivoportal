<?php
namespace Vivo\CMS\UI\Content\Editor\File;

/**
 * Interface for Editor adapters
 *
 */
interface DataEditorInterface
{
	/**
	 * Checks if the data has changed
	 * @return bool
	 */
	public function dataChanged();

	/**
	 * Sets data
	 * @param string $data
	 */
	public function setData($data);

	/**
	 * Returns the changed data
	 */
	public function getData();
}
