<?php
namespace Vivo\Repository\Exception;

/**
 * Document has no published content.
 * @author miroslav.hajek
 */
class NoPublishedContentException extends \Exception implements ExceptionInterface {

	/**
	 * @param string $ident Entity identification (path, UUID or symbolic reference).
	 */
// 	public function __construct($ident) {
// 		parent::__construct(403, 'no_published_content', array($ident));
// 	}

}
