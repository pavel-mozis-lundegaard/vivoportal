<?php
namespace Vivo\Router;

use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

/**
 * Simple router, that is used in routerchain only for setting hostname in RouteMatch.  
 * @author kormik
 */
class Hostname implements RouteInterface {

	public function match(Request $request) {
		return new RouteMatch( array('host' => $request->getUri()->getHost()));
	}

	public static function factory($options = array()) {
		return new static();
	}

	public function assemble(array $params = array(), array $options = array()) {
		// TODO: Auto-generated method stub

	}

	public function getAssembledParams() {
		return $this->assembledParams;
	}
}
