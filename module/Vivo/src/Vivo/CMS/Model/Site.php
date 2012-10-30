<?php
namespace Vivo\CMS\Model;

/**
 * Represents web site as VIVO model.
 */
class Site extends Folder {
	/**
	 * @var string Security domain name.
	 */
	protected $domain;
	/**
	 * @var string Parent site name.
	 * @example META-SITE
	 */
	protected $parentSite;
	/**
	 * @var array Hosts are domain address under which you accessed the site.
	 */
	protected $hosts = array();

	/**
	 * @param string Path to entity.
	 * @param Vivo\CMS\Model\Entity\Security
	 */
	function __construct($path = null, $security = null) {
		parent::__construct($path, $security);
	}

	public function setDomain($domain) {
		$this->domain = $domain;
	}

	public function setHosts(array $hosts) {
		$this->hosts = $hosts;
	}

	public function getHosts() {
		return $this->hosts;
	}
}
