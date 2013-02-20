<?php
namespace Vivo\Util;

use Zend\EventManager\Event;

/**
 * Redirect event
 */
class RedirectEvent extends Event
{
    const EVENT_REDIRECT = 'redirect';

    /**
     * Url for redirection.
     * @var string
     */
    protected $url;

    /**
     * @param string $url
     * @param array $params
     *
     * @example $params = array ('status_code' = 302, 'immediately' => true)
     */
    public function __construct($url = null, $params = null)
    {
        $this->url = $url;
        parent::__construct(self::EVENT_REDIRECT, null, $params);
    }

    /**
     * Returns url for redirect.
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
