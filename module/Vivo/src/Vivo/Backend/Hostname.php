<?php
namespace Vivo\Backend;

use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ArrayUtils;

use Traversable;

/**
 * Simple router, that is used in router chain only for setting hostname in RouteMatch.
 */
class Hostname implements RouteInterface
{
    /**
     * Configured backend hosts
     * @var array
     */
    protected $hosts    = array();

    public function __construct(array $hosts)
    {
        $this->hosts    = $hosts;
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
            throw new Exception\InvalidArgumentException(
                sprintf('%s: Expects an array or Traversable set of options'));
        }
        if (!isset($options['hosts'])) {
            throw new Exception\InvalidArgumentException(sprintf("%s: Missing 'hosts' in options array"));
        }
        return new static($options['hosts']);
    }

    public function assemble(array $params = array(), array $options = array())
    {
        return '';
    }

    public function getAssembledParams()
    {
        return array();
    }
}
