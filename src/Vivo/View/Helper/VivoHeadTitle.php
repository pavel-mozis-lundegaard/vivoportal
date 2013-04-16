<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Api\Document as DocumentApi;
use Vivo\CMS\Api\CMS as CmsApi;
use Vivo\CMS\Model\Site;
use Vivo\CMS\Model\Document as DocumentModel;

use Zend\View\Helper\AbstractHelper;

/**
 * VivoHeadTitle
 * Renders head title suitable for the <title> tag
 */
class VivoHeadTitle extends AbstractHelper
{
    /**#@+
     * Placeholders for typical title components
     * These placeholders are to be used as values in $this->options['order'] array
     */
    const PH_SITE   = 'site';
    const PH_PARENT = 'parent';
    const PH_DOC    = 'doc';
    /**#@-*/

    /**
     * Document API
     * @var DocumentApi
     */
    protected $documentApi;

    /**
     * CMS API
     * @var CmsApi
     */
    protected $cmsApi;

    /**
     * Current site model
     * @var Site
     */
    protected $site;

    /**
     * View helper options
     * @var array
     */
    protected $options  = array(
        'separator' => ' | ',
        'elements'     => array(self::PH_DOC, self::PH_SITE),
    );

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param DocumentApi $documentApi
     * @param Site $site
     * @param array $options
     */
    public function __construct(CmsApi $cmsApi, DocumentApi $documentApi, Site $site, array $options = array())
    {
        $this->cmsApi       = $cmsApi;
        $this->documentApi  = $documentApi;
        $this->site         = $site;
        $this->options      = array_merge($this->options, $options);
    }

    /**
     * Invoke the helper as a PhpRenderer method call
     * @param DocumentModel $doc
     * @param array $options
     * @return string
     */
    public function __invoke(DocumentModel $doc = null, array $options = array())
    {
        if (is_null($doc)) {
            return $this;
        }
        $rendered   = $this->render($doc, $options);
        return $rendered;
    }

    /**
     * Returns the title elements in an array
     * @throws \Exception
     */
    public function getElements(DocumentModel $doc)
    {
        if ($this->cmsApi->getEntityRelPath($doc->getPath()) != '/') {
            $parent         = $this->documentApi->getParentDocument($doc);
            $parentTitle    = $parent->getTitle();
        } else {
            $parentTitle    = null;
        }
        $components = array(
            self::PH_SITE   => $this->site->getTitle(),
            self::PH_PARENT => $parentTitle,
            self::PH_DOC    => $doc->getTitle(),
        );
        $branchDocs     = $this->documentApi->getDocumentsOnBranch($doc, '/', true, true);
        $branchTitles   = array();
        foreach ($branchDocs as $branchDoc) {
            $branchTitles[] = $branchDoc->getTitle();
        }
        $components['branch_titles'] = $branchTitles;
        return $components;
    }

    /**
     * Renders the view helper
     * @param DocumentModel $doc
     * @param array $options
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    public function render(DocumentModel $doc, array $options = array())
    {
        $options    = array_merge($this->options, $options);
        /** @var $headTitleZf \Zend\View\Helper\HeadTitle */
        $headTitleZf    = $this->getView()->plugin('head_title');
        $headTitleZf->setSeparator($options['separator']);
        foreach ($options['elements'] as $element) {
            switch ($element) {
                case self::PH_SITE:
                    $siteTitle  = $this->site->getTitle();
                    if ($siteTitle) {
                        $headTitleZf->__invoke($siteTitle);
                    }
                    break;
                case self::PH_PARENT:
                    if ($this->cmsApi->getEntityRelPath($doc->getPath()) != '/') {
                        $parent = $this->documentApi->getParentDocument($doc);
                        $headTitleZf->__invoke($parent->getTitle());
                    }
                    break;
                case self::PH_DOC:
                    $headTitleZf->__invoke($doc->getTitle());
                    break;
                default:
                    throw new Exception\InvalidArgumentException(
                        sprintf("%s: Unsupported element '%s' specified in order option", __METHOD__, $element));
                    break;
            }
        }
        $rendered   = $headTitleZf->toString();
        return $rendered;
    }
}
