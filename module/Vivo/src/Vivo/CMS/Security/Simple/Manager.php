<?php
namespace Vivo\CMS\Security\Simple;

use Vivo\CMS\Security\AbstractManager;

use Zend\Session\SessionManager;

use stdClass;

/**
 * Manager
 * Simple security manager
 * For development purposes, implements just authentication
 */
class Manager extends AbstractManager
{
    /**
     * Credentials for successful authentication
     * @var array
     */
    protected $options  = array(
        'security_domain'   => 'VIVO',
        'username'          => 'vivo.user',
        'password'          => 'password',
    );

    /**
     * Constructor
     * @param \Zend\Session\SessionManager $sessionManager
     * @param array $options Credentials for successful authentication
     */
    public function __construct(SessionManager $sessionManager, array $options = array())
    {
        parent::__construct($sessionManager);
        $this->options  = array_merge($this->options, $options);
    }

    /**
     * Returns roles in security domain.
     * @param string $domain Domain name.
     * @return array
     */
    function getRoles($domain)
    {
        // TODO: Implement getRoles() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $rolename
     */
    function addRole($domain, $rolename)
    {
        // TODO: Implement addRole() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $rolename
     */
    function removeRole($domain, $rolename)
    {
        // TODO: Implement removeRole() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $rolename
     * @return array
     */
    function getRoleAccessRights($domain, $rolename)
    {
        // TODO: Implement getRoleAccessRights() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $rolename
     * @param string $access_right
     */
    function grantRoleAccessRight($domain, $rolename, $access_right)
    {
        // TODO: Implement grantRoleAccessRight() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $rolename
     * @param string $access_right
     */
    function revokeRoleAccessRight($domain, $rolename, $access_right)
    {
        // TODO: Implement revokeRoleAccessRight() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain Domain name.
     * @param string $pattern
     * @return array
     */
    function getUsers($domain, $pattern = false)
    {
        // TODO: Implement getUsers() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $username
     * @return stdClass
     */
    function getUser($domain, $username)
    {
        // TODO: Implement getUser() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param stdClass $user
     */
    function addUser($domain, $user)
    {
        // TODO: Implement addUser() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param stdClass $user
     */
    function updateUser($domain, $user)
    {
        // TODO: Implement updateUser() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param stdClass $user
     */
    function removeUser($domain, $user)
    {
        // TODO: Implement removeUser() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @return array
     */
    function getGroups($domain)
    {
        // TODO: Implement getGroups() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $groupname
     */
    function addGroup($domain, $groupname)
    {
        // TODO: Implement addGroup() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $groupname
     */
    function removeGroup($domain, $groupname)
    {
        // TODO: Implement removeGroup() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param stdClass $user
     * @param string $groupname
     */
    function addUserToGroup($domain, $user, $groupname)
    {
        // TODO: Implement addUserToGroup() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param stdClass $user
     * @param string $groupname
     */
    function removeUserFromGroup($domain, $user, $groupname)
    {
        // TODO: Implement removeUserFromGroup() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain
     * @param string $username
     * @return array Array of stdClasses.
     */
    function getUserGroups($domain, $username)
    {
        // TODO: Implement getUserGroups() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain Security domain name.
     * @param string $username User login.
     * @return stdClass
     */
    function getUserProfile($domain, $username)
    {
        // TODO: Implement getUserProfile() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * @param string $domain Security domain name.
     * @param string $username User login.
     * @param stdClass $profile
     */
    function setUserProfile($domain, $username, $profile)
    {
        // TODO: Implement setUserProfile() method.
        throw new \Exception(sprintf('%s not implemented!', __METHOD__));
    }

    /**
     * Authenticates a user
     * Returns user object upon successful authentication or null otherwise
     * @param string $domain
     * @param string $username
     * @param string $password
     * @return stdClass|null
     */
    function authenticate($domain, $username, $password)
    {
        if (strtolower($domain) == strtolower($this->options['security_domain'])
            && strtolower($username) == strtolower($this->options['username'])
            && $password == $this->options['password']) {
            //Authentication ok
            $user           = new stdClass();
            $user->username = $this->options['username'];
        } else {
            //Authentication failed
            $user           = null;
        }
        $this->setUserPrincipal($user);
        return $user;
    }
}