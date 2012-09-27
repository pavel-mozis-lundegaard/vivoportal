<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id$
 */
namespace Vivo\CMS;

/**
 * Entity not found.
 * @author mhajek
 */
class EntityNotFoundException extends Exception {

	/**
	 * @param string $ident Entity identification (path, UUID or symbolic reference).
	 */
	public function __construct($ident) {
		parent::__construct(404, 'entity_not_found', array($ident));
	}

}
