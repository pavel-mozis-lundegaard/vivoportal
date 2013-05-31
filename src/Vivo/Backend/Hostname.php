<?php
namespace Vivo\Backend;

use Traversable;
use Vivo\Backend\Exception\InvalidArgumentException;

use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Simple router, that is used to match backend hostname.
 * Host names for the backend are defined in config.
 */
class Hostname implements RouteInterface
{

    /**
     * Configured backend hosts
     * @var array
     */
    protected $hosts = array();

    public function __construct(array $hosts)
    {
        $this->hosts = $hosts;
    }

    public function match(Request $request)
    {
        if (in_array($request->getUri()->getHost(), $this->hosts)) {
            return new RouteMatch(array());
        }
        return null;
    }

    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new InvalidArgumentException(
            sprintf('%s: Expects an array or Traversable set of options'));
        }
        if (!isset($options['hosts'])) {
            throw new InvalidArgumentException(sprintf("%s: Missing 'hosts' in options array"));
        }
        return new static($options['hosts']);
    }

    /**
     * Asemble route.
     *
     * Method sets uri host to backend hostname. If there is more then one hostname, the first one is selected.
     *
     * @param array $params
     * @param array $options
     * @return string
     */
    public function assemble(array $params = array(), array $options = array())
    {
        if (isset($options['uri'])) {
            $options['uri']->setHost(reset($this->hosts));
        }

        return '';
    }

    public function getAssembledParams()
    {
        return array();
    }
}
