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
     * @var ExplorerInterface
     */
    protected $explorer;

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
        $site = $this->explorer->getSite();
        return $this->cmsApi->getSiteEntity('', $site);
    }

    /**
     * Creates document tree and set it to view.
     */
    protected function refreshTree()
    {
        $rootDocument = $this->getSiteRoot();
        $this->view->tree = $this->getDocumentTree($rootDocument,
                $this->explorer->getEntity()->getPath());
    }

    /**
     *
     * @param ExplorerInterface $explorer
     */
    public function setExplorer(ExplorerInterface $explorer)
    {
        $this->explorer = $explorer;
    }

    /**
     *
     * @param string $relPath
     */
    public function set($relPath)
    {
        $this->explorer->setEntityByRelPath($relPath);
    }

    /**
     * Show
     * @param type $relPath
     */
    public function showMore($relPath)
    {
        $this->explorer->setEntityByRelPath($relPath);
        $this->explorer->setCurrent('browser');
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
    protected function getDocumentTree(Folder $rootFolder, $expandedPath = '', $maxItems = 10)
    {

        $que = new \SplQueue();
        $tree = new DataTree($rootFolder);
        $root = new DataTree();
        $root->addChild($tree);
        $que->push($tree);
        while(!$que->isEmpty()){
            /* @var $node  DataTree */
            $node = $que->pop();

            $child = $node->value;
            if (!$child) continue;
            $children = $this->documentApi->getChildDocuments($child);
            $a = array ();
            $a['document'] = $child;
            $a['published'] = true;
            $a['content_type'] = '';
            $a['level'] = $node->getDeep();
            $a['rel_path'] = $this->cmsApi->getEntityRelPath($child);
            $a['active'] = $child->getPath() == $expandedPath;
            $a['expandable'] = (boolean) count($children);
            $a['count'] = count($children);

            if ($node->getParent()->value['document']) {
                $a['parent_rel_path'] = $this->cmsApi->getEntityRelPath(
                        $node->getParent()->value['document']);
            }
            $node->value = $a;
            $i = 0;
            $expand = $expandedPath == $child->getPath() ||
                    (strpos($expandedPath, $child->getPath(). '/') !== false);
            if ($expand) {
                foreach ($children as $child) {
                    if (++$i > $maxItems) {
                        break;
                    }
                    $childNode = new DataTree($child);
                    $node->addChild($childNode);
                    $que->push($childNode);
                }
            }
        }
        return $root;
    }
}
