<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\UI\Component;

class Viewer extends Component
{
    public function view()
    {
        $this->getView()->entity = $this->getParent()->getEntity();
        return parent::view();
    }
}
