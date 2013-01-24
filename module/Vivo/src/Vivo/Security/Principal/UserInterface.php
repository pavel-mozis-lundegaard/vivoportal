<?php
namespace Vivo\Security\Principal;

/**
 * UserInterface
 */
interface UserInterface extends PrincipalInterface
{
    //password, email, active, expires

    /**
     * Returns user's security domain
     * @return string
     */
    public function getDomain();

    /**
     * Returns user's username
     * @return string
     */
    public function getUsername();

    /**
     * Returns password hash
     * @return string
     */
    public function getPasswordHash();

    /**
     * Returns user's full name
     * @return string
     */
    public function getFullName();

    /**
     * Returns user's e-mail
     * @return string
     */
    public function getEmail();

    /**
     * Is the user active
     * @return boolean
     */
    public function isActive();

    public function get
}