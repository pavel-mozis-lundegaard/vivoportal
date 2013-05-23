<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Model;

use Zend\View\Helper\AbstractHelper;

/**
 * OverviewTitle
 * View helper - returns title suitable for overview
 */
class OverviewTitle extends AbstractHelper
{
    /**
     * Invoke the view helper as the PHPRendere method call
     * @param Model\Entity $entity
     * @return string|$this
     */
    public function __invoke(Model\Entity $entity = null)
    {
        if (is_null($entity)) {
            return $this;
        }
        $output = $this->render($entity);
        return $output;
    }

    /**
     * Returns entity title suitable for display in an overview
     * @param Model\Entity $entity
     * @return string
     */
    public function render(Model\Entity $entity)
    {
        if ($entity instanceof Model\Document) {
            //Document
            $title  = $entity->getOverviewTitle() ?: $entity->getTitle();
        } elseif ($entity instanceof Model\Folder) {
            //Folder
            $title  = $entity->getTitle();
        } else {
            //Other entity types
            $title  = $entity->getName();
        }
        return $title;
    }
}
