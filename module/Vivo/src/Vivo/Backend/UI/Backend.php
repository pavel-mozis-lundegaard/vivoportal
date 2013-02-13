<?php
namespace Vivo\Backend\UI;

use Vivo\UI\Text;
use Vivo\UI\ComponentContainer;

class Backend extends ComponentContainer{

    public function __construct()
    {
        $this->addComponent(new Text('test'), 'text');
    }
}
