<?php
/**
 * Vivo framework
 *
 * Copyright (c) 2012 author(s) listed below.
 */
namespace Vivo;

/**
 */
interface TransactionalInterface  {

	public function begin();

	public function commit();

	public function rollback();

}
