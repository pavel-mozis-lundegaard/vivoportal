<?php
namespace Vivo\CMS\Model;

/**
 * Base class for all VIVO models.
 */
class Content extends Entity {
	/**
	 * @var string Workflow state
	 */
	protected $state;
	/**
	 * @var DateTime Date for automatic state change by cron.
	 */
	protected $stateChange;
	/**
	 * @var bool
	 */
	protected $recursive;

	/**
	 * Setting default values.
	 * @param string $path Entity path.
	 */
	public function __construct($path = null) {
		parent::__construct($path);
	}

	public function getState() {
		return $this->state;
	}

	public function setState($state) {
		$this->state = $state;
	}

	/**
	 * Gets content version.
	 * @return int
	 */
	public function getVersion() {
		return ($p = strrpos($this->getPath(), '/')) ? substr($this->getPath(), $p + 1) : 0;
	}

	/**
	 * Sets version.
	 * @param int $version
	 */
	public function setVersion($version) {
		$this->path = substr($this->getPath(), 0, strrpos($this->getPath(), '/') + 1).$version;
	}

	/**
	 * Gets content index. Index means number of a content in the Multicontent Document.
	 * @return int
	 */
	public function getIndex() {
		return preg_match('~\/Contents\.(\d{1,2})\/~', $this->getPath(), $matches) ? intval($matches[1]) : false;
	}

	/**
	 * Returns relative path of the content.
	 * @return string Content relative path
	 */
	public function getRelativePath() {
		return 'Contents'.(($index = $this->getIndex()) ? '.'.$index  : '').'/'.$this->getVersion();
	}

}
