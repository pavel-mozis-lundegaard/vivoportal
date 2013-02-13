<?php
namespace Vivo\Backend\UI\Explorer;

use Vivo\CMS\Api;
use Vivo\CMS\Model\Folder;
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
     * @var Api\CMS
     */
    protected $cmsApi;

    /**
     * @var Api\Document
     */
    protected $documentApi;

    /**
     * Constuctor.
     * @param Api\CMS $cmsApi
     * @param Api\Document $documentApi
     */
    public function __construct(Api\CMS $cmsApi, Api\Document $documentApi)
    {
        $this->cmsApi = $cmsApi;
        $this->documentApi = $documentApi;
    }

    /**
     * Returns root document of site.
     * @return \Vivo\CMS\Model\Folder
     */
    protected function getSiteRoot()
    {
        $site = $this->entityManager->getSite();
        return $this->cmsApi->getSiteEntity('', $site);
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

    /**
     *
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @param string $relPath
     */
    public function set($relPath)
    {
        $this->entityManager->setEntityByRelPath($relPath);
    }

    /**
     * (non-PHPdoc)
     * @see \Vivo\UI\Component::view()
     */
    public function view()
    {
        $this->refreshTree();
        return parent::view();
    }


    /**
     * Returs tree of document using DataTree structure.
     *
     * @param Folder $rootFolder
     * @param string $expandedPath
     * @return \Vivo\Util\DataTree
     */
    protected function getDocumentTree(Folder $rootFolder, $expandedPath = '' /*$maxDepth = 10, $maxItems = 20*/)
    {
        $que = new \SplQueue();
        $tree = new DataTree($rootFolder);
        $root = new DataTree();
        $root->addChild($tree);
        $que->push($tree);
        while(!$que->isEmpty()){
            /* @var $node  DataTree */
            $node = $que->pop();
            foreach ($this->documentApi->getChildDocuments($node->getValue()) as $child) {
                $childNode = new DataTree($child);
                $node->addChild($childNode);
                if (strpos($expandedPath, $child->getPath()) !== false) {
                    $que->push($childNode);
                }
            }
        }

        $i = new \RecursiveIteratorIterator($root, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($i as $node) {
            //add document aditional information
            $a = array ();
            $a['document'] = $node->value;
            $a['published'] = true;
            $a['content_type'] = '';
            $a['level'] = $node->getDeep();
            $a['rel_path'] = $this->cmsApi->getEntityRelPath($node->value);
            $a['active'] = $node->value->getPath() == $expandedPath;
            $a['expandable'] = (boolean) count($this->documentApi->getChildDocuments($node->value));
            $node->value = $a;
        }
        return $root;
    }
}
