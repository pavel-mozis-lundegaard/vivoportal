<?php
namespace Vivo\Security;

use Zend\Session;

use stdClass;

/**
 * AbstractManager
 * Security Manager defines base methods for working with users, roles and rights in Vivo applications.
 * All new security managers with must extends this
 */
abstract class AbstractManager
{

    /* default users */
    const USER_ANONYMOUS = 'anonymous';
    const USER_ADMINISTRATOR = 'administrator';
    const USER_SYSTEM = 'system';

    /* default user groups */
    const GROUP_ANYONE = 'Anyone';

    /* default (builtin) security domain */
    const DOMAIN_VIVO = 'VIVO';

    /**
     * @var Container
     */
    protected $session;

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
     * @param string $pattern
     * @return array
     */
    abstract function getUsers($domain, $pattern = false);

    /**
     * @param string $domain
     * @param string $username
     * @return stdClass
     */
    abstract function getUser($domain, $username);

    /**
     * @param string $domain
     * @param stdClass $user
     */
    abstract function addUser($domain, $user);

    /**
     * @param string $domain
     * @param stdClass $user
     */
    abstract function updateUser($domain, $user);

    /**
     * @param string $domain
     * @param stdClass $user
     */
    abstract function removeUser($domain, $user);

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
     * @param stdClass $user
     * @param string $groupname
     */
    abstract function addUserToGroup($domain, $user, $groupname);

    /**
     * @param string $domain
     * @param stdClass $user
     * @param string $groupname
     */
    abstract function removeUserFromGroup($domain, $user, $groupname);

    /**
     * @param string $domain
     * @param string $username
     * @return array Array of stdClasses.
     */
    abstract function getUserGroups($domain, $username);

    /**
     * Constructor
     * @param Session\SessionManager $sessionManager
     */
    public function __construct(Session\SessionManager $sessionManager)
    {
        $this->session = new Session\Container(__CLASS__, $sessionManager);
    }

    /**
     * @param string $domain Security domain name.
     * @param string $username User login.
     * @param string $groupname
     * @return bool
     */
    public function isUserInGroup($domain, $username, $groupname)
    {
        return ($groupname == self::GROUP_ANYONE)
                || ($username == self::USER_SYSTEM);
    }

    /**
     * Returns user principal class. Principal class is a basic model of the currently
     * logged-on client (backend) or site visitor.
     * @return stdClass|null
     */
    public function getUserPrincipal()
    {
        $principal = null;
        if (isset($this->session['security.principal'])
                && $this->session['security.principal'] instanceof \stdClass) {
            $principal = $this->session['security.principal'];
        }
        if (!$principal && isset($_SERVER['REMOTE_USER'])
                && ($remote_user = $_SERVER['REMOTE_USER'])) {
            $principal = new \stdClass;
            if ($pos = strpos($remote_user, '@')) {
                // kerberos format
                $principal->domain = substr($remote_user, $pos + 1);
                $principal->username = strtolower(substr($remote_user, 0, $pos));
            } else {
                // winbind format
                $remote_user = str_replace('+', '\\', $remote_user); // nahrazeni standardniho winbind separatoru na linuxu
                $pos = strpos($remote_user, '\\');
                if ($pos === false) { // format bez domeny
                    $principal->username = $remote_user;
                } else {
                    $principal->domain = substr($remote_user, 0, $pos);
                    $principal->username = strtolower(
                            substr($remote_user, $pos + 1));
                }
            }
            $principal->fullname = $this
                    ->getUserFullname($principal->domain, $principal->username);
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
     * @param stdClass $principal
     * @return bool
     */
    public function setUserPrincipal($principal)
    {
        if (is_object($this->session) && $principal) {
            $this->session->getManager()->regenerateId();
        }
        return $this->session['security.principal'] = $principal;
    }

    public function removeUserPrincipal()
    {
        $this->setUserPrincipal(null);
    }

    /**
     * @return string
     */
    public function getPrincipalDomain()
    {
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
     * @param string $domain Security domain name.
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
