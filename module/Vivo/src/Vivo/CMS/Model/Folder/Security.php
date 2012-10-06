<?php
namespace Vivo\CMS\Model\Folder;

use Vivo\CMS\Security\Manager;

/**
 * Represenst security model for document.
 * According to assign the groups into the roles allowed to view the document for visitor editing or viewing in backend etc.
 */
class Security {
	/**
	 * Assign users respectively user groups to roles.
	 * <code>
	 * 'rolename' => array('user_or_group1', ..., 'user_or_groupX')
	 * </code>
	 * @var array Associative array.
	 */
	public $roles = array();
	/**
	 * Explicit granting access rights to users or user groups.
	 * <code>
	 * 'access_right' => array('user_or_group1', ..., 'user_or_groupX')
	 * </code>
	 * @var array Associative array.
	 */
	public $allow = array();
	/**
	 * Explicit denial access rights to users or user groups.
	 * <dode>
	 * 'access_right' => array('user_or_group1', ..., 'user_or_groupX')
	 * </code>
	 * @var array Associative array.
	 */
	public $deny = array();

	/**
	 * @param array $roles
	 * @param array $allow
	 * @param array $deny
	 */
	public function __construct($roles = array(), $allow = array(), $deny = array()) {
		$this->roles =
			$roles; /* ? :
			array(
				Manager::ROLE_VISITOR => array(Manager::GROUP_ANYONE),
				Manager::ROLE_ADMINISTRATOR => array(Manager::GROUP_ADMINISTRATORS)
			);*/

		$this->allow = $allow;
		$this->deny = $deny;
	}

	/**
	 * Get groups from roles, allow and deny arrays.
	 * @return array
	 */
	public function getGroups() {
		$groups = array();
		foreach ($this->roles as $rolename) {
			$groups = array_merge($groups, $rolename);
		}
		foreach ($this->allow as $rolename) {
			$groups = array_merge($groups, $rolename);
		}
		foreach ($this->deny as $rolename) {
			$groups = array_merge($groups, $rolename);
		}
		$groups = array_unique($groups);
		sort($groups);
		return $groups;
	}

}


