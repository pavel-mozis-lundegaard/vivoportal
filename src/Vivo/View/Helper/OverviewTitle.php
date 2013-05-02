<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Model\Entity;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Model\Folder;

use Zend\View\Helper\AbstractHelper;

/**
 * OverviewTitle
 * View helper - returns title suitable for overview
 */
class OverviewTitle extends AbstractHelper
{
    /**
     * Invoke the view helper as the PHPRendere method call
     * @param Entity $entity
     * @return string|$this
     */
    public function __invoke(Entity $entity = null)
    {
        if (is_null($entity)) {
            return $this;
        }
        $output = $this->render($entity);
        return $output;
    }

    /**
     * Returns entity title suitable for display in an overview
     * @param Entity $entity
     * @return string
     */
    public function render(Entity $entity)
    {
        if ($entity instanceof Document) {
            //Document
            $title  = $entity->getOverviewTitle() ?: $entity->getTitle();
        } elseif ($entity instanceof Folder) {
            //Folder
            $title  = $entity->getTitle();
        } else {
            //Other entity types
            $title  = $entity->getName();
        }
        return $title;
    }
}
