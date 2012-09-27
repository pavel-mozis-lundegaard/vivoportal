<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id$
 */
namespace Vivo\CMS;

/**
 * Document has no content.
 * @author mhajek
 */
class NoContentException extends Exception {

	/**
	 * @param string string $ident Entity identification (path, UUID or symbolic reference).
	 */
	public function __construct($ident) {
		parent::__construct(403, 'no_content', array($ident));
	}

}
