<?php
namespace Vivo;

/**
 * @author miroslav.hajek
 */
interface TransactionalInterface  {

	public function begin();

	public function commit();

	public function rollback();

}
