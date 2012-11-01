<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: File.php 1921 2012-01-16 13:01:55Z mhajek $
 */
namespace Vivo\CMS\Model\Content;

use Vivo\CMS;
use Vivo\CMS\Model;
use Vivo\Util;

/**
 * The file is a basic content type. If a document with the content layout settings,
 * the file appears in it (an image directly on the page, other types of file download link),
 * otherwise it will always download the file directly for example from the Files folder.
 *
 * @author tzajicek
 */
class File extends Model\Content //implements Model\IResource, IAutoVersionable {
{

	const FRONT_COMPONENT = 'Vivo\CMS\UI\Content\File';
	const EDITOR_COMPONENT = 'Vivo\CMS\UI\Content\Editor\File';

	/**
	 * @var string
	 */
	static $DEFAULT_MIME_TYPE;
	/**
	 * @var string
	 */
	public $mime_type;
	/**
	 * @var string Original filename.
	 */
	public $filename;

	/**
	 * Setting default values
	 * @param string $path Entity path
	 */
	function __construct($path = null) {
//		parent::__construct($path);
//		$this->mime_type = self::$DEFAULT_MIME_TYPE;
	}

	/**
	 * Retuns the logical file name
	 * @return string
	 */
	function name() {
		return $this->getDocument()->getName().'.'.Util\MIME::getExt($this->mime_type);
	}

	/**
	 * Returns the URL address at which the resource file accessed.
	 * @return string
	 */
	function url() {
		return $this->getDocument()->getURL(); // obsah typu soubor je vzdy pristupny pod URL dokumentu
	}

	/**
	 * Returns resource file
	 * @return string
	 */
	function get() {
		return CMS::$repository->getFile($this->getResourcePath());
	}

	/**
	 * Loads data from VIVO CMS repository
	 * @see $this->getResourcePath()
	 * @return string
	 */
	function read() {
		return CMS::$repository->readFile($this->getResourcePath(), false);
	}

	/**
	 * Saves data into VIVO CMS repository
	 * @see $this->getResourcePath()
	 * @param string $data
	 */
	function write($data) {
		CMS::$repository->writeFile($this->getResourcePath(), $data);
	}

	/**
	 * Resource URL
	 * @return string
	 */
	function getResourceURL() {
		return parent::getURL().'/resource.'.Util\MIME::getExt($this->mime_type);
	}

	/**
	 * Resource file path
	 * @return string
	 */
	function getResourcePath() {
		return $this->path.'/resource.'.Util\MIME::getExt($this->mime_type);
	}

	/**
	 * File icon
	 * @return string Icon file name with extension
	 */
	function getIcon() {
		$icon = 'File';
		if (in_array($ext = Util\MIME::getExt($this->mime_type), CMS\UI::$MIME_TYPE_ICONS))
			$icon .= '.'.$ext;
		return $icon;
	}

	public function getMimeType() {
	    return $this->mime_type;
	}

	public function getFilename() {
	    return $this->filename;
	}

}