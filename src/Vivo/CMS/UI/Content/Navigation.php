<?php
namespace Vivo\CMS\UI\Content;

use Vivo\CMS\Api\CMS as CmsApi;
use Vivo\CMS\Api\Document as DocumentApi;
use Vivo\CMS\Model\Site;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Model\Content\Navigation as NavigationModel;
use Vivo\CMS\UI\Exception;
use Vivo\CMS\Navigation\Page\Cms as CmsNavPage;

use Vivo\CMS\UI\Component;

use Zend\Navigation\AbstractContainer as AbstractNavigationContainer;
use Zend\Navigation\Navigation as NavigationContainer;

/**
 * Navigation UI component
 */
class Navigation extends Component
{
    /**
     * CMS Api
     * @var CmsApi
     */
    protected $cmsApi;

    /**
     * Document API
     * @var DocumentApi
     */
    protected $documentApi;

    /**
     * Site model
     * @var Site
     */
    protected $site;

    /**
     * Navigation model (i.e. the content)
     * @var NavigationModel
     */
    protected $navModel;

    /**
     * Navigation
     * @var AbstractNavigationContainer
     */
    protected $navigation;

    /**
     * Constructor
     * @param CmsApi $cmsApi
     * @param DocumentApi $documentApi
     * @param Site $site
     */
    public function __construct(CmsApi $cmsApi, DocumentApi $documentApi, Site $site)
    {
        $this->cmsApi       = $cmsApi;
        $this->documentApi  = $documentApi;
        $this->site         = $site;
    }

    public function init()
    {
        if (!$this->content instanceof NavigationModel) {
            throw new Exception\DomainException(
                sprintf("%s: Content model must be of type 'Vivo\\CMS\\Model\\Content\\Navigation'", __METHOD__));
        }
        $this->navModel = $this->content;
    }

    public function view()
    {
        $navigation = $this->getNavigation();
        $this->getView()->testVar       = 'FooBar';
        $this->getView()->navigation    = $navigation;
        return parent::view();
    }

    /**
     * Returns navigation container
     * @throws \Vivo\CMS\UI\Exception\DomainException
     * @return AbstractNavigationContainer
     */
    public function getNavigation()
    {
        if (is_null($this->navigation)) {
            switch ($this->navModel->getType()) {
                case NavigationModel::TYPE_ROOT:
                    $rootDoc    = $this->cmsApi->getSiteEntity($this->navModel->getRoot(), $this->site);
                    $documents  = $this->buildDocArray($rootDoc,
                                                       $this->navModel->getLevels(),
                                                       $this->navModel->includeRoot());
                    break;
                case NavigationModel::TYPE_RQ_DOC:
                    $documents  = $this->buildDocArray($this->cmsEvent->getDocument(),
                                                       $this->navModel->getLevels(),
                                                       $this->navModel->includeRoot());
                    break;
                case NavigationModel::TYPE_ENUM:
                    $documents  = $this->navModel->getEnumeratedDocs();
                    break;
                default:
                    throw new Exception\DomainException(
                        sprintf("%s: Unsupported navigation type '%s'", __METHOD__, $this->navModel->getType()));
                    break;
            }
            //Create the navigation container
            $this->navigation   = new NavigationContainer();
            $pages              = $this->buildNavPages($documents);
            $this->navigation->setPages($pages);
        }
        return $this->navigation;
    }

    /**
     * Builds document array starting at $root
     * @param Document $root Root document
     * @param int $levels
     * @param bool $includeRoot
     * @return array
     */
    protected function buildDocArray(Document $root, $levels = null, $includeRoot = false)
    {
        $docArray   = array();
        if (is_null($levels) || $levels > 0) {
            if (!is_null($levels)) {
                $levels--;
            }
            $children   = $this->documentApi->getChildDocuments($root);
            foreach ($children as $key => $child) {
                //TODO - Revise: Keep only documents, not folders
                if (!$child instanceof Document) {
                    unset($children[$key]);
                    continue;
                }
                $rec    = array(
                    'doc_path'  => $this->cmsApi->getEntityRelPath($child),
                    'children'  => $this->buildDocArray($child, $levels, false),
                );
                $docArray[] = $rec;
            }
            if ($includeRoot) {
                $docArray   = array(
                    array(
                        'doc_path'  => $root->getPath(),
                        'children'  => $docArray,
                    ),
                );
            }
        }
        return $docArray;
    }

    /**
     * Builds navigation pages from the supplied documents structure
     * @param array $documents For structure see property Vivo\CMS\Model\Content::$enumeratedDocs
     * @throws \Vivo\CMS\UI\Exception\UnexpectedValueException
     * @throws \Vivo\CMS\UI\Exception\InvalidArgumentException
     * @return CmsNavPage[]
     */
    protected function buildNavPages(array $documents = array())
    {
        $pages      = array();
        $currentDoc = $this->cmsEvent->getDocument();
        foreach ($documents as $docArray) {
            if (!is_array($docArray)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Document record must be represented by an array", __METHOD__));
            }
            if (!array_key_exists('doc_path', $docArray)) {
                throw new Exception\InvalidArgumentException(
                    sprintf("%s: Document array must contain 'doc_path' key", __METHOD__));
            }
            $docPath    = $docArray['doc_path'];
            $doc    = $this->cmsApi->getSiteEntity($docPath, $this->site);
            if (!$doc instanceof Document) {
                throw new Exception\UnexpectedValueException(
                    sprintf("%s: Entity specified by path '%s' is not a document", __METHOD__, $docPath));
            }
            $docRelPath     = $this->cmsApi->getEntityRelPath($doc);
            $pageOptions    = array(
                'sitePath'      => $docRelPath,
                'label'         => $doc->getTitle(),
                'active'        => $this->cmsApi->getEntityRelPath($currentDoc) == $docRelPath,
                'document'      => $doc,
            );
            $page           = new CmsNavPage($pageOptions);
            if (array_key_exists('children', $docArray)
                    && is_array($docArray['children'])
                    && count($docArray['children']) > 0) {
                $children   = $this->buildNavPages($docArray['children']);
                $page->setPages($children);
            }
            $pages[]    = $page;
        }
        return $pages;
    }
}
