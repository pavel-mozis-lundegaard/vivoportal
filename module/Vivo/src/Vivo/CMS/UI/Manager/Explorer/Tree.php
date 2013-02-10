<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Doctrine\ORM\EntityManager;

use Vivo\CMS\Model\Folder;
use Vivo\CMS\Api\CMS;
use Vivo\UI\Component;
use Vivo\Util\DataTree;

/**
 * Tree of documents.
 */
class Tree extends Component
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var CMS
     */
    protected $cms;

    /**
     * Constuctor.
     * @param CMS $cms
     */
    public function __construct(CMS $cms)
    {
        $this->cms = $cms;
    }

    /**
     * Returns root document of site.
     * @return \Vivo\CMS\Model\Folder
     */
    protected function getSiteRoot()
    {
        $site = $this->entityManager->getSite();
        return $this->cms->getSiteDocument('', $site);
    }

    /**
     * Creates document tree and set it to view.
     */
    protected function refreshTree()
    {
        $rootDocument = $this->getSiteRoot();
        $tree = $this->getDocumentTree($rootDocument, $this->entityManager->getEntity()->getPath());
        $this->view->tree = new \RecursiveIteratorIterator($tree, \RecursiveIteratorIterator::SELF_FIRST);
    }

    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
//         $this->entityManager->getEventManager()
//                 ->attach('setEntity', array($this, 'onEntityChange'));
    }

//     public function onEntityChange()
//     {

//     }

    /**
     *
     * @param string $relPath
     */
    public function set($relPath)
    {
        $this->entityManager->setEntityByRelPath($relPath);
    }

    public function view()
    {
        $this->refreshTree();
        return parent::view();
    }


    public function getDocumentTree(Folder $rootFolder, $expandedPath = '' /*$maxDepth = 10, $maxItems = 20*/)
    {
        $que = new \SplQueue();
        $tree = new DataTree($rootFolder);
        $root = new DataTree();
        $root->addChild($tree);
        $que->push($tree);
        while(!$que->isEmpty()){
            /* @var $node  DataTree */
            $node = $que->pop();
            $document = $node->getValue();
            foreach ($this->cms->getChildDocuments($document) as $child) {
                $childNode = new DataTree($child);
                $node->addChild($childNode);
                if (strpos($expandedPath, $child->getPath()) !== false) {
                    $que->push($childNode);
                }
            }
        }

        $i = new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($i as $node) {
            $a = array ();
            $a['document'] = $node->value;
            $a['published'] = true;
            $a['content_type'] = '';
            $a['level'] = $node->getDeep();
            $a['rel_path'] = $this->cms->getEntityRelPath($node->value);
            $a['active'] = $node->value->getPath() == $expandedPath;
            $a['expandable'] = (boolean) count($this->cms->getChildDocuments($node->value));
            $node->value = $a;
        }
        return $root;
    }
}
