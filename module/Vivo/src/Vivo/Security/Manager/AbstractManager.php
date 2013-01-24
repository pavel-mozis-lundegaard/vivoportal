<?php
namespace Vivo\Security\Manager;

use Vivo\Security\Principal;

use Zend\Session;

/**
 * AbstractManager
 * Security Manager defines base methods for working with users, roles and rights in Vivo applications.
 * All new security managers must extends this class
 */
abstract class AbstractManager
{
    /* default users */
    const USER_ANONYMOUS        = 'anonymous';
    const USER_ADMINISTRATOR    = 'administrator';
    const USER_SYSTEM           = 'system';

    /* default user groups */
    const GROUP_ANYONE          = 'Anyone';

    /* default (builtin) security domain */
    const DOMAIN_VIVO           = 'VIVO';

    /**
     * @var Session\Container
     */
    protected $session;

    /**
     * Constructor
     * @param Session\SessionManager $sessionManager
     */
    public function __construct(Session\SessionManager $sessionManager)
    {
        $this->session = new Session\Container(__CLASS__, $sessionManager);
    }

    /**
     * Returns roles in security domain.
     * @param string $domain Domain name.
     * @return array
     */
    abstract function getRoles($domain);

    /**
     * @param string $domain
     * @param string $rolename
     */
    abstract function addRole($domain, $rolename);

    /**
     * @param string $domain
     * @param string $rolename
     */
    abstract function removeRole($domain, $rolename);

    /**
     * @param string $domain
     * @param string $rolename
     * @return array
     */
    abstract function getRoleAccessRights($domain, $rolename);

    /**
     * @param string $domain
     * @param string $rolename
     * @param string $access_right
     */
    abstract function grantRoleAccessRight($domain, $rolename, $access_right);

    /**
     * @param string $domain
     * @param string $rolename
     * @param string $access_right
     */
    abstract function revokeRoleAccessRight($domain, $rolename, $access_right);

    /**
     * @param string $domain Domain name.
     * @param string|bool $pattern
     * @return array
     */
    abstract function getUsers($domain, $pattern = false);

    /**
     * @param string $domain
     * @param string $username
     * @return Principal\PrincipalInterface
     */
    abstract function getUser($domain, $username);

    /**
     * @param string $domain
     * @param Principal\PrincipalInterface $user
     */
    abstract function addUser($domain, Principal\PrincipalInterface $user);

    /**
     * @param string $domain
     * @param Principal\PrincipalInterface $user
     */
    abstract function updateUser($domain, Principal\PrincipalInterface $user);

    /**
     * @param string $domain
     * @param Principal\PrincipalInterface $user
     */
    abstract function removeUser($domain, Principal\PrincipalInterface $user);

    /**
     * @param string $domain
     * @return array
     */
    abstract function getGroups($domain);

    /**
     * @param string $domain
     * @param string $groupname
     */
    abstract function addGroup($domain, $groupname);

    /**
     * @param string $domain
     * @param string $groupname
     */
    abstract function removeGroup($domain, $groupname);

    /**
     * @param string $domain
     * @param Principal\PrincipalInterface $user
     * @param string $groupname
     */
    abstract function addUserToGroup($domain, Principal\PrincipalInterface $user, $groupname);

    /**
     * @param string $domain
     * @param Principal\PrincipalInterface $user
     * @param string $groupname
     */
    abstract function removeUserFromGroup($domain, Principal\PrincipalInterface $user, $groupname);

    /**
     * @param string $domain
     * @param string $username
     * @return array Array of stdClasses.
     */
    abstract function getUserGroups($domain, $username);

    /**
     * @param string $domain Security domain name.
     * @param string $username User login.
     * @param string $groupName
     * @return bool
     */
    public function isUserInGroup($domain, $username, $groupName)
    {
        return ($groupName == self::GROUP_ANYONE)
                || ($username == self::USER_SYSTEM);
    }

    /**
     * Returns user principal of the currently logged-on client (backend) or site visitor
     * Returns null if no user is logged on
     * @return Principal\PrincipalInterface|null
     */
    public function getUserPrincipal()
    {
        $principal = null;
        if (isset($this->session['security.principal'])
                && $this->session['security.principal'] instanceof Principal\PrincipalInterface) {
            $principal = $this->session['security.principal'];
        }
        //TODO - refactor not to use $_SERVER
        if (!$principal && isset($_SERVER['REMOTE_USER']) && ($remoteUser = $_SERVER['REMOTE_USER'])) {
            $principal = new Principal\User();
            if ($pos = strpos($remoteUser, '@')) {
                //Kerberos format
                $principal->setDomain(substr($remoteUser, $pos + 1));
                $principal->setUsername(strtolower(substr($remoteUser, 0, $pos)));
            } else {
                //Winbind format
                //Replace the standard winbind separator on Linux
                $remoteUser = str_replace('+', '\\', $remoteUser);
                $pos        = strpos($remoteUser, '\\');
                if ($pos === false) {
                    //Format without domain
                    $principal->setUsername($remoteUser);
                } else {
                    $principal->setDomain(substr($remoteUser, 0, $pos));
                    $principal->setUsername(strtolower(substr($remoteUser, $pos + 1)));
                }
            }
            $fullName   = $this->getUserFullname($principal->getDomain(), $principal->getUsername());
            $principal->setFullName($fullName);
            $this->setUserPrincipal($principal);
        }
        return $principal;
    }

    /**
     * @param string $domain
     * @param string $username
     * @return string
     */
    public function getUserFullname($domain, $username)
    {
        return $username;
    }

    /**
     * Sets user principal and returns it
     * @param Principal\PrincipalInterface|null $principal
     * @return Principal\PrincipalInterface|null
     */
    public function setUserPrincipal(Principal\PrincipalInterface $principal = null)
    {
        if ($principal) {
            $this->session->getManager()->regenerateId();
        }
        $this->session['security.principal'] = $principal;
        return $principal;
    }

    /**
     * Removes user principal
     */
    public function removeUserPrincipal()
    {
        $this->setUserPrincipal(null);
    }

    /**
     * @return string
     */
    public function getPrincipalDomain()
    {
        $principal = $this->getUserPrincipal();

        return ($principal = $this->getUserPrincipal()) ? $principal->domain
                : self::DOMAIN_VIVO;
    }

    /**
     * @return string
     */
    public function getPrincipalUsername()
    {
        return ($principal = $this->getUserPrincipal()) ? $principal->username
                : self::USER_ANONYMOUS;
    }

    /**
     * @param array $names
     * @return bool
     */
    protected function isPrincipalMemberOf($names)
    {
        return $this
                ->isMemberOf($this->getPrincipalDomain(),
                        $this->getPrincipalUsername(), $names);
    }

    /**
     * @param string $domain Security domain name.
     * @param string $name User login.
     * @param array $names
     * @return bool
     */
    protected function isMemberOf($domain, $name, $names)
    {
        if (!empty($names)) {
            if (in_array($name, $names) || in_array(self::GROUP_ANYONE, $names))
                return true;
            foreach ($this->getGroups($domain) as $group) {
                foreach ($names as $groupname) {
                    if (($groupname == $group->groupname)
                            && $this->isUserInGroup($domain, $name, $groupname))
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $domain Security domain name.
     * @param string $username User login.
     * @return stdClass
     */
    abstract function getUserProfile($domain, $username);

    /**
     * @param string $domain Security domain name.
     * @param string $username User login.
     * @param stdClass $profile
     */
    abstract function setUserProfile($domain, $username, $profile);

    /**
     * Authenticates a user
     * Returns user object upon successful authentication or null otherwise
     * @param string $domain
     * @param string $username
     * @param string $password
     * @return stdClass|null
     */
    abstract function authenticate($domain, $username, $password);

    /**
     * @return bool
     */
    public function authenticateAsSystem()
    {
        $principal = new \stdClass;
        $principal->domain = self::DOMAIN_VIVO;
        $principal->username = self::USER_SYSTEM;
        return $this->setUserPrincipal($principal);
    }

    /* L1 cache functions */

    /**
     * @var array L1 cache.
     */
    public $cache = array();

    public function &cache(/*$keys...*/)
    {
        $array = &$this->cache;
        $args = func_get_args();
        if (is_array($args[0]))
            $args = $args[0];
        foreach ($args as $key) {
            if (!array_key_exists($key, $array) || !is_array($array[$key]))
                $array[$key] = array();
            $array = &$array[$key];
        }
        return $array;
    }

    public function flush(/*$keys...*/)
    {
        $array = &$this->cache(func_get_args());
        $array = array();
    }
}
