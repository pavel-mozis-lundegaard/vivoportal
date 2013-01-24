<?php
namespace Vivo\Security\Principal;

/**
 * User
 * User object
 */
class User implements UserInterface
{
    /**
     * Username
     * @var string
     */
    protected $username;

    /**
     * User's full name
     * @var string
     */
    protected $fullName;

    /**
     * Security domain
     * @var string
     */
    protected $domain;

    /**
     * Sets username
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Returns username
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns security principal name
     * @return string
     */
    public function getName()
    {
        return $this->username;
    }

    /**
     * Sets security domain
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Returns security domain
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets user's full name
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * Returns user's full name
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }
}