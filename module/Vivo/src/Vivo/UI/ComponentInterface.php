<?php
namespace Vivo\UI;
/**
 * @author kormik
 *
 */
interface ComponentInterface {
	public function init();
	public function view();
	public function done();
	public function getName();
	public function getparent();
}
