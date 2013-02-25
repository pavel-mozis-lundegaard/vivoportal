<?php
namespace Vivo\Backend;

use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Simple router, that is used in routerchain only for setting hostname in RouteMatch.
 */
class Hostname implements RouteInterface
{

    protected $options;


    public function __construct($options)
    {
        $this->options = $options;
    }

    public function match(Request $request)
    {
        if (in_array($request->getUri()->getHost(), $this->options['hosts']))
            return new RouteMatch(array());

        return null;
    }

    public static function factory($options = array())
    {
        return new static($options);
    }

    public function assemble(array $params = array(), array $options = array())
    {
        return '';
    }

    public function getAssembledParams()
    {
        return array ();
    }
}
