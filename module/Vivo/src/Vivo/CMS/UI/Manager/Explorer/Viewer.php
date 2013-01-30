<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\UI\Component;

class Viewer extends  Component
{
    protected $explorer;

    public function setExplorer(EntityManagerInterface $explorer)
    {
        $this->explorer = $explorer;
    }
}
