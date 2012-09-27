<?php
/**
 * Vivo CMS
 * Copyright (c) 2009 author(s) listed below.
 *
 * @version $Id: Exception.php 1354 2011-05-23 07:45:52Z tzajicek $
 */
namespace Vivo\CMS;

/**
 * Listing of folder (document) content is not allowed.
 * @author mhajek
 */
class ListingNotAllowedException extends Exception {

	public function __construct() {
		parent::__construct(500, 'listing_not_allowed');
	}

}
