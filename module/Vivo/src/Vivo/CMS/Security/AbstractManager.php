<?php
namespace Vivo\CMS\Security;

use Vivo\CMS\Exception\Exception;
use Vivo\Security\AbstractManager as AbstractParentManager;

/**
 * AbstractManager
 * Security Manager defines base methods for working with users, roles and rights in Vivo CMS application.
 * All new Managers must extends this
 */
abstract class AbstractManager extends AbstractParentManager
{

    /* default global roles */
    const ROLE_VISITOR = 'Visitor';
    const ROLE_PUBLISHER = 'Publisher';
    const ROLE_ADMINISTRATOR = 'Administrator';

    /* default user groups */
    const GROUP_MANAGERS = 'Managers';
    const GROUP_PUBLISHERS = 'Publishers';
    const GROUP_DEVELOPERS = 'Developers';
    const GROUP_ADMINISTRATORS = 'Administrators';

    /**
     * Security domain of current site.
     * @var string
     * @todo remove dependecy to site security domain
     */
    protected $domain;

    /**
     * Returns current site domain.
     * @return string
     */
    public function getDomain()
    {
        return $this->domain ? : self::DOMAIN_VIVO;
    }

    /**
     * Set security domain.
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param Vivo\CMS\Model\Document $entity
     * @param string $access_right		Access right name.
     * @param bool $throw_exception
     * @param string $name				User login.
     * @param string $domain			Security domain name.
     * @throws Vivo\CMS\Exception		401, Access denied
     * @return bool
     */
    public function authorize($entity, $access_right, $throw_exception = true,
            $name = null, $domain = null)
    {
        $authorized = 0;
        if (!$name) {
            $name = $this->getPrincipalUsername();
        }
        if (!$domain) {
            $domain = $this->getDomain();
        }

        $security = $entity->security;
        // no security definition
        if (!$security) {
            $authorized = 1;
        }
        // administrator (allways authorized)
        if (($authorized == 0) && (($name == self::USER_ADMINISTRATOR)
                        || ($name == self::GROUP_ADMINISTRATORS)
                        || $this
                                ->isUserInGroup($domain, $name,
                                        self::GROUP_ADMINISTRATORS))) {
            $authorized = 1;
        }
        // delete by owner
        if (($authorized == 0) && ($access_right == 'Delete')
                && ($name == $entity->createdBy)) {
            $authorized = 1;
        }
        // explicit deny
        if ($authorized == 0) {
            foreach ($security->deny as $deny_right => $names) {
                if (($deny_right == $access_right)
                        && $this->isMemberOf($domain, $name, $names)) {
                    $authorized = -1;
                    break;
                }
            }
        }
        // explicit allow
        if ($authorized == 0) {
            foreach ($security->allow as $allow_right => $names) {
                if (($allow_right == $access_right)
                        && $this->isMemberOf($domain, $name, $names)) {
                    $authorized = 1;
                    break;
                }
            }
        }
        // role membership
        if ($authorized == 0) {
            foreach ($security->roles as $rolename => $names) {
                if (in_array($access_right,
                        $this->getRoleAccessRights($domain, $rolename))
                        && $this->isMemberOf($domain, $name, $names)) {
                    $authorized = 1;
                    break;
                }
            }
        }

        // result
        if ($authorized == 1) {
            return true;
        } else if (!$throw_exception) {
            return false;
        } else {
            throw new Exception(
                    sprintf('Access denied (entity: %s, domain: %s, name: %s, right: %s).',
                    $entity->path, $domain, $name, $access_right));
        }
    }
}

