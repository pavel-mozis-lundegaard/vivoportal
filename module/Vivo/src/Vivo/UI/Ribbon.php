<?php
namespace Vivo\UI;

/**
 * @author peter.krajcar
 */
class Ribbon extends TabContainer
{
	public function view() {
        $this->view->name = $this->getName();
        $this->view->components = array_keys($this->components);
        return parent::view();
    }
}
