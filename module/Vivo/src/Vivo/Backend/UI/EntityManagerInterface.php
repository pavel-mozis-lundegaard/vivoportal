<?php
namespace Vivo\Backend\UI;

use Vivo\CMS\Model\Entity;

/**
 * @deprecated Use ExplorerInterface instead this one.
 */

interface EntityManagerInterface
{
    public function setEntity(Entity $entity);
    public function getEntity();
    public function setEntityByRelPath($relpath);
}
