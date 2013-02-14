<?php
namespace Vivo\Backend\UI;

use Vivo\CMS\Model\Entity;

interface EntityManagerInterface
{
    public function setEntity(Entity $entity);
    public function getEntity();
    public function setEntityByRelPath($relpath);
}
