<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Model\Content;

/**
 * Interface for Editor adapters
 */
interface ResourceEditorInterface
{
	/**
	* Checks if the data has changed
	* @return bool
	*/
	public function dataChanged();

	/**
	 * Returns the changed data
	 */
	public function getData();
}
