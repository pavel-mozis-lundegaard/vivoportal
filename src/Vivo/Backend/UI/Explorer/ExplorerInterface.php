<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\Model\Entity;

interface ExplorerInterface
{
    public function setEntity(Entity $entity);
    public function setEntityByRelPath($relPath);
    public function getEntity();
    public function getSite();
    public function setCurrent($name);
}
