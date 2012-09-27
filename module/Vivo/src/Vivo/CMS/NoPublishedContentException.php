<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id$
 */
namespace Vivo\CMS;

/**
 * Document has no published content.
 * @author mhajek
 */
class NoPublishedContentException extends Exception {

	/**
	 * @param string $ident Entity identification (path, UUID or symbolic reference).
	 */
	public function __construct($ident) {
		parent::__construct(403, 'no_published_content', array($ident));
	}

}
