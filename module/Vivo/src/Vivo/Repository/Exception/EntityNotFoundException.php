<?php
namespace Vivo\Repository\Exception;

/**
 * @author miroslav.hajek
 */
class EntityNotFoundException extends \Exception implements ExceptionInterface {

	/**
	 * @param string $ident Entity identification (path, UUID or symbolic reference).
	 */
// 	public function __construct($ident) {
// 		parent::__construct(404, 'entity_not_found', array($ident));
// 	}

}
