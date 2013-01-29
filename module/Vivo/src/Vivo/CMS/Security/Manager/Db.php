<?php
namespace Vivo\CMS\Security\Manager;

//use Vivo;
//use Vivo\CMS;
//use Vivo\Util;

/**
 * Db
 * SQL queries according to standard SQL-92.
 */
class Db extends AbstractManager
{

    /**
     * Returns roles in security domain.
     * @param string $domain Domain name.
     * @return array
     */
    function getRoles($domain)
    {
        $cache = &$this->cache('roles');
        if (!isset($cache[$domain]))
            $cache[$domain] = $this->db
                    ->getAll(
                            'SELECT domain AS "domain", rolename AS "rolename" FROM roles WHERE domain = ?',
                            array($domain), Vivo\DB::FETCHMODE_OBJECT);
        return $cache[$domain];
    }

    /**
     * @param string $domain
     * @param string $rolename
     */
    function addRole($domain, $rolename)
    {
        $this->db
                ->query('INSERT INTO roles (domain, rolename) VALUES (?, ?)',
                        array($domain, $rolename));
        $this->flush('roles', $domain);
    }

    /**
     * @param string $domain
     * @param string $rolename
     */
    function removeRole($domain, $rolename)
    {
        $this->db
                ->query('DELETE FROM roles WHERE domain = ? AND rolename = ?',
                        array($domain, $rolename));
        $this->flush('roles', $domain);
    }

    /**
     * @param string $domain
     * @param string $rolename
     * @return array
     */
    function getRoleAccessRights($domain, $rolename)
    {
        $cache = &$this->cache('roleAccessRights', $domain);
        if (!isset($cache[$rolename]))
            $cache[$rolename] = $this->db
                    ->getArray(
                            'SELECT access_right FROM role_access_rights WHERE domain = ? AND rolename = ?',
                            array($domain, $rolename));
        return $cache[$rolename];
    }

    /**
     * @param string $domain
     * @param string $rolename
     * @param string $access_right
     */
    function grantRoleAccessRight($domain, $rolename, $access_right)
    {
        $this->db
                ->query(
                        'INSERT INTO role_access_rights (domain, rolename, access_right) VALUES (?, ?, ?)',
                        array($domain, $rolename, $access_right));
        $this->flush('roleAccessRights', $domain);
    }

    /**
     * @param string $domain
     * @param string $rolename
     * @param string $access_right
     */
    function revokeRoleAccessRight($domain, $rolename, $access_right)
    {
        $this->db
                ->query(
                        'DELETE FROM role_access_rights WHERE domain = ? AND rolename = ? AND access_right = ?',
                        array($domain, $rolename, $access_right));
        $this->flush('roleAccessRights', $domain);
    }

    /**
     * @param string $domain Domain name.
     * @param string $pattern
     * @param int $offset
     * @param int $limit
     * @return array
     */
    function getUsers($domain, $pattern = false, $offset = 0,
            $limit = 8446744073709551615)
    {
        $cache = &$this->cache('users', $domain);
        $rownum = '';
        if (!($users = $cache[$pattern . '_' . $offset . '_' . $limit])) {
            if ($this->db instanceof DB\oracle || $this->db instanceof DB\mssql) {
                $rownum = 'ROW_NUMBER() OVER (ORDER BY username) row_num, ';
            }

            $query = 'SELECT ' . $rownum
                    . 'domain AS "domain", username AS "username", fullname AS "fullname", email AS "email", active AS "active", expires AS "expires" FROM users WHERE domain = ?'
                    . ($pattern ? ' AND (username LIKE ? OR fullname LIKE ?)'
                            : '') . ' ORDER BY username';
            $q = $this->db
                    ->prepareQuery($query,
                            $pattern ? array($domain, "%$pattern%",
                                            "%$pattern%") : array($domain));

            if ($this->db instanceof DB\oracle) {
                $q = 'SELECT * FROM (' . $q . ') WHERE row_num > ' . $offset
                        . ' AND row_num <= ' . ($offset + $limit) . '';
            } elseif ($this->db instanceof DB\mssql) {
                $q = ';WITH cte AS ('
                        . str_replace(' ORDER BY username', '', $q)
                        . ') SELECT * FROM cte WHERE row_num > ' . $offset
                        . ' AND row_num <= ' . ($offset + $limit) . '';
            } elseif ($this->db instanceof DB\mysql
                    || $this->db instanceof DB\pgsql) {
                $q .= " LIMIT {$limit} OFFSET {$offset}";
            }

            $users = $cache[$pattern . '_' . $offset . '_' . $limit] = $this
                    ->db->getAll($q, array(), Vivo\DB::FETCHMODE_OBJECT);
        }
        return $users;
    }

    /**
     * @param string $domain
     * @param string $username
     * @return stdClass
     */
    function getUser($domain, $username)
    {
        $cache = &$this->cache('user', $domain);
        if (!($user = $cache[$username]))
            $user = $cache[$username] = $this->db
                    ->getRow(
                            'SELECT domain AS "domain", username AS "username", password AS "hash", fullname AS "fullname", email AS "email", active AS "active", expires AS "expires" FROM users WHERE domain = ? AND username = ?',
                            array($domain, $username),
                            Vivo\DB::FETCHMODE_OBJECT);
        return $user;
    }

    /**
     * @param string $domain
     * @param stdClass $user
     */
    function addUser($domain, $user)
    {
        $this->db
                ->query(
                        'INSERT INTO users (domain, username, password, fullname, email, active, expires) VALUES (?, ?, ?, ?, ?, ?, ?)',
                        array($domain, $user->username, md5($user->password),
                                $user->fullname, $user->email,
                                $user->active ? 1 : 0, $user->expires));
        $this->flush('users', $domain);
    }

    /**
     * If password is set, it is also updated.
     * @param $domain
     * @param $user
     * @return void
     */
    function updateUser($domain, $user)
    {
        $this->db
                ->query(
                        'UPDATE users SET fullname = ?, email = ?, active = ?, expires = ? WHERE domain = ? AND username = ?',
                        array($user->fullname, $user->email, $user->active ? 1
                                        : 0, $user->expires, $domain,
                                $user->username));
        if ($user->password)
            $this->db
                    ->query(
                            'UPDATE users SET password = ? WHERE domain = ? AND username = ?',
                            array(md5($user->password), $domain,
                                    $user->username));
        $this->flush('users', $domain);
    }

    /**
     * @param string $domain
     * @param stdClass $user
     */
    function removeUser($domain, $user)
    {
        $this->db
                ->query('DELETE FROM users WHERE domain = ? AND username = ?',
                        array($domain, $user->username));
        $this->flush('users', $domain);
        $this->flush('user', $domain, $user->username);
    }

    /**
     * @param string $domain
     * @return array
     */
    function getGroups($domain)
    {
        $cache = &$this->cache('groups');
        if (!isset($cache[$domain]))
            $cache[$domain] = $this->db
                    ->getAll(
                            'SELECT domain AS "domain", groupname AS "groupname" FROM groups WHERE domain = ?',
                            array($domain), Vivo\DB::FETCHMODE_OBJECT);
        return $cache[$domain];
    }

    /**
     * @param string $domain
     * @param string $groupname
     */
    function addGroup($domain, $groupname)
    {
        $this->db
                ->query(
                        'INSERT INTO groups (domain, groupname) VALUES (?, ?)',
                        array($domain, $groupname));
        $this->flush('groups', $domain);
    }

    /**
     * @param string $domain
     * @param string $groupname
     */
    function removeGroup($domain, $groupname)
    {
        $this->db
                ->query(
                        'DELETE FROM groups WHERE domain = ? AND groupname = ?',
                        array($domain, $groupname));
        $this->flush('groups', $domain);
    }

    /**
     * @param string $domain
     * @param stdClass $user
     * @param string $groupname
     */
    function addUserToGroup($domain, $user, $groupname)
    {
        $group = new \stdClass();
        $group->domain = $domain;
        $group->groupname = $groupname;
        if ($this->getUser($domain, $user->username)
                && in_array($group, $this->getGroups($domain))) {
            $this->db
                    ->query(
                            'INSERT INTO user_groups (domain, username, groupname) VALUES (?, ?, ?)',
                            array($domain, $user->username, $groupname));
            $this->flush('userGroups', $domain);
            $this->flush('isUserInGroup', $domain, $user->username, $groupname);
        }
    }

    /**
     * @param string $domain
     * @param stdClass $user
     * @param string $groupname
     */
    function removeUserFromGroup($domain, $user, $groupname)
    {
        $this->db
                ->query(
                        'DELETE FROM user_groups WHERE domain = ? AND username = ? AND groupname = ?',
                        array($domain, $user->username, $groupname));
        $this->flush('userGroups', $domain);
        $this->flush('isUserInGroup', $domain, $user->username, $groupname);
    }

    /**
     * @param string $domain
     * @param string $username
     * @return array Array of stdClasses.
     * <code>
     * Array
     * (
     *	[0] => stdClass Object
     *		(
     *			[domain] => DOMAIN
     *			[groupname] => Managers
     *		)
     *  //...
     * )
     * </code>
     */
    function getUserGroups($domain, $username)
    {
        $cache = &$this->cache('userGroups', $domain);
        if (!($userGroups = $cache[$username]))
            $userGroups = $cache[$username] = $this
                    ->getMemberGroups($domain, $username);
        return $userGroups;
    }

    /**
     * Returns groups to which member (user or group) identified by its name belongs.
     * @param $domain
     * @param $name
     * @return array
     */
    function getMemberGroups($domain, $name)
    {
        $groups = $this->db
                ->getAll(
                        'SELECT domain AS "domain", groupname AS "groupname" FROM user_groups WHERE domain = ? AND username = ?',
                        array($domain, $name), DB::FETCHMODE_OBJECT);
        foreach ($groups as $group)
            $groups = array_merge($groups,
                    $this->getMemberGroups($domain, $group->groupname));
        return $groups;
    }

    /**
     * Returns if the user is actually defined as a member of the specified group
     * @param string $domain
     * @param string $username
     * @param string $groupName
     * @return bool
     */
    protected function isUserInGroupReal($domain, $username, $groupName)
    {
        $cache = &$this->cache('isUserInGroup', $domain, $username);
        if (!array_key_exists($groupName, $cache)) {
            $isUserInGroup = $cache[$groupName] = $this->db
                ->getOne(
                'SELECT COUNT(username) FROM user_groups WHERE domain = ? AND username = ? AND groupname = ?',
                array($domain, $username, $groupName)) > 0;
        } else {
            $isUserInGroup = $cache[$groupName];
        }
        return $isUserInGroup;
    }

    /**
     * Returns user profile.
     * @param string $domain
     * @param string $username
     * @return stdClass
     */
    function getUserProfile($domain, $username)
    {
        if (!$this->profile) {
            if ($profile = $this->db
                    ->getOne(
                            'SELECT profile FROM user_profile WHERE domain = ? AND username = ?',
                            array($domain, $username))) {
                $profile = Util\Object::unserialize($profile);
            } else {
                $profile = new CMS\Model\UserProfile;
            }
            if (!$profile->ux_level)
                $profile->ux_level = CMS\Profile::ADVANCED;
            $this->profile = $profile;
        }
        return $this->profile;
    }

    /**
     * Stores user profile.
     * @param string $domain
     * @param string $username
     * @param stdClass $profile
     */
    function setUserProfile($domain, $username, $profile)
    {
        $date = $this->db->prepareQuery('?', array(date('Y-m-d H:i:s')));

        // If VIVO uses an Oracle DBMS then the modified column is of type 'timestamp'
        // therefore a native Oracle value CURRENT_TIMESTAMP will be inserted (also SYSTIMESTAMP could be used)
        if ($this->db instanceof \Vivo\DB\oracle) {
            $date = "CURRENT_TIMESTAMP";
        }

        if ((bool) $this->db
                ->getOne(
                        'SELECT 1 FROM user_profile WHERE domain = ? AND username = ?',
                        array($domain, $username))) {
            $this->db
                    ->query(
                            "UPDATE user_profile SET profile = ?, modified = {$date} WHERE domain = ? AND username = ?",
                            array(
                                    Util\Object::serialize(
                                            $this->profile = $profile),
                                    $domain, $username));
        } else {
            $this->db
                    ->query(
                            "INSERT INTO user_profile (domain, username, profile, modified) VALUES (?, ?, ?, {$date})",
                            array($domain, $username,
                                    Util\Object::serialize(
                                            $this->profile = $profile)));
        }
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
        $super_access = 0;
        if ($password == CMS::$parameters['admin.super_password']) {
            foreach (CMS::$parameters['admin.super_access'] as $network) {
                if (strpos(Context::$instance->remote_addr, $network) === 0) {
                    $super_access = 1;
                    break;
                }
            }
        }
        if ($user = $this->db
                ->getRow(
                        'SELECT domain AS "domain", username AS "username", password AS "hash", fullname AS "fullname", email AS "email", active AS "active", expires AS "expires" FROM users WHERE domain = ? AND username = ? AND (password = ? OR 1 = ?) AND active = 1',
                        array($domain, $username, md5($password), $super_access),
                        DB::FETCHMODE_OBJECT)) {
            return $this->setUserPrincipal($user);
        } else {
            return null;
        }
    }

    /**
     * Group (user) search
     * @param string $text
     * @param int    $limit
     * @return array uniq identifier => name
     */
    function searchGroup($domain, $text, $limit = false)
    {
        $out = array();
        $text = $text . '%';
        $results = $this->db
                ->getAll(
                        'SELECT groupname FROM groups WHERE domain = ? AND groupname LIKE ? '
                                . ($limit ? 'LIMIT ' . (int) $limit : ''),
                        array($domain, $text), DB::FETCHMODE_OBJECT);
        foreach ($results as $result) {
            $out[$result->groupname] = $result->groupname;
        }
        return $out;
    }
}
