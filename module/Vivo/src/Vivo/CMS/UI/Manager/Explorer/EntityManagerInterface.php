<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\Model\Entity;

interface EntityManagerInterface
{
    public function setEntity(Entity $entity);
    public function getEntity();
    public function setEntityByRelPath($relpath);
}
