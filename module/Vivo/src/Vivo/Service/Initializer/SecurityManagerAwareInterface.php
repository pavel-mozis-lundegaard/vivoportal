<?php
namespace Vivo\Service\Initializer;

use Vivo\CMS\Security\Manager\AbstractManager as AbstractSecurityManager;

/**
 * SecurityManagerAwareInterface
 */
interface SecurityManagerAwareInterface
{
    /**
     * Sets the Security Manager
     * @param AbstractSecurityManager $securityManager
     * @return void
     */
    public function setSecurityManager(AbstractSecurityManager $securityManager);
}
