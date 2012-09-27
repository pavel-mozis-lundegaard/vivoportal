<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id$
 */
namespace Vivo\CMS;

/**
 * CMS exception.
 * @author mhajek
 */
class ResourceNotFoundException extends Exception {

	public function __construct($code = null, $name = null, $args = array()) {
		parent::__construct(403, 'file_not_found', $args);
	}

}
