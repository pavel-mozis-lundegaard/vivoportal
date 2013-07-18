<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Model;
use Vivo\CMS\Util\DocumentUrlHelper;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for getting document url
 */
class Document extends AbstractHelper
{
    /**
     * Helper options
     * @var array
     */
    private $options = array();

    /**
     * Document url helper
     * @var DocumentUrlHelper
     */
    private $documentUrlHelper;

    /**
     * Constructor
     * @param \Vivo\CMS\Api\CMS $cmsApi
     * @param array $options
     */
    public function __construct(DocumentUrlHelper $documentUrlHelper, $options = array())
    {
        $this->documentUrlHelper = $documentUrlHelper;
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Returns document url
     * @param \Vivo\CMS\Model\Document $document
     * @return string
     */
    public function __invoke(Model\Document $document)
    {
        return $this->documentUrlHelper->getDocumentUrl($document);
    }
}
