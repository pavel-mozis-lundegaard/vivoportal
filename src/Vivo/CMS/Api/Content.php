<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Model;


/**
 * Content
 * Content API
 */
class Content
{
    /**
     * Repository
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * CMS API
     * @var CMS
     */
    protected $cmsApi;

    /**
     * Constructor
     * @param CMS $cmsApi
     * @param \Vivo\Repository\RepositoryInterface $repository
     */
    public function __construct(CMS $cmsApi,
                                Document $documentApi,
                                RepositoryInterface $repository)
    {
        $this->cmsApi                       = $cmsApi;
        $this->documentApi                  = $documentApi;
        $this->repository                   = $repository;
    }
    
    /**
     * Get published content simple class (mainly for css)
     * 
     * @param \Vivo\CMS\Model\Document $document
     * @return string ('Overview'|'Component'|'Link'|...)
     */
    public function getContentSimpleClass(Model\Document $document)
    {
        $content = $this->documentApi->getPublishedContentTypes($child);
        $path = explode('\\', $content[0]);
        return $path[count($path) - 1];
    }
    
}