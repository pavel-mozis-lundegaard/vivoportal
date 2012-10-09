<?php
namespace Vivo\Repository\Exception;

/**
 * Document has no content.
 * @author miroslav.hajek
 */
class NoContentException extends \Exception implements ExceptionInterface {

	/**
	 * @param string string $ident Entity identification (path, UUID or symbolic reference).
	 */
// 	public function __construct($ident) {
// 		parent::__construct(403, 'no_content', array($ident));
// 	}

}
