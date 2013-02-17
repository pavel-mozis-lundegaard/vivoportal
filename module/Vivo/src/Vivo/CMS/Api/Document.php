<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model;
use Vivo\Repository\RepositoryInterface;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\CMS\Workflow\Factory as WorkflowFactory;
use Vivo\CMS\Workflow\WorkflowInterface;
use Vivo\CMS\Exception;

/**
 * Document
 * Document API
 */
class Document implements DocumentInterface
{
    /**
     * Repository
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Path Builder
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * Workflow factory
     * @var WorkflowFactory
     */
    protected $workflowFactory;

    /**
     * CMS API
     * @var CMS
     */
    protected $cms;

    /**
     * Constructor
     * @param CMS $cms
     * @param \Vivo\Repository\RepositoryInterface $repository
     * @param \Vivo\Storage\PathBuilder\PathBuilderInterface $pathBuilder
     * @param \Vivo\CMS\Workflow\Factory $workflowFactory
     */
    public function __construct(CMS $cms,
                                RepositoryInterface $repository,
                                PathBuilderInterface $pathBuilder,
                                WorkflowFactory $workflowFactory)
    {
        $this->cms              = $cms;
        $this->repository       = $repository;
        $this->pathBuilder      = $pathBuilder;
        $this->workflowFactory  = $workflowFactory;
    }

    /**
     * Returns array of published contents of given document.
     * @param Model\Document $document
     * @return Model\Content[]
     */
    public function getPublishedContents(Model\Document $document)
    {
        $containers = $this->repository->getChildren($document, 'Vivo\CMS\Model\ContentContainer');
        $contents   = array();
        usort($containers,
            function (Model\ContentContainer $a, Model\ContentContainer $b)
            {
                return $a->getOrder() < $b->getOrder();
            });
        foreach ($containers as $container) {
            if ($content = $this->getPublishedContent($container)) {
                $contents[] = $content;
            }
        }
        return $contents;
    }

    /**
     * Returns array of published content types (class names of published contents)
     * If there are no published contents, returns an empty array
     * @param \Vivo\CMS\Model\Document $document
     * @return string[]
     */
    public function getPublishedContentTypes(Model\Document $document)
    {
        $publishedContents      = $this->getPublishedContents($document);
        $publishedContentTypes  = array();
        /** @var $publishedContent Model\Content */
        foreach ($publishedContents as $publishedContent) {
            $publishedContentTypes[]    = get_class($publishedContent);
        }
        return $publishedContentTypes;
    }

    /**
     * Finds published content in ContentContainer,
     * @param Model\ContentContainer $container
     * @return Model\Content|boolean
     * @throws Exception\LogicException when there is more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container)
    {
        $result = array();
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        foreach ($contents as $content) {
            /* @var $content Model\Content */
            if ($content->getState() == WorkflowInterface::STATE_PUBLISHED) {
                $result[] = $content;
            }
        }
        if (count($result) == 1) {
            return $result[0];
        } elseif (count($result) == 0) {
            return false;
        } else {
            throw new Exception\LogicException(
                sprintf("%s: The ContentContainer '%s' contains more than one published content.",
                    __METHOD__, $container->getPath()));
        }
    }

    /**
     * @param Model\Content $content
     */
    public function publishContent(Model\Content $content)
    {
        $document   = $this->getContentDocument($content);
        $oldContent = $this->getPublishedContent($document, $content->getIndex());
        if ($oldContent) {
            $oldContent->setState(WorkflowInterface::STATE_ARCHIVED);
            $this->cms->saveEntity($oldContent, false);
        }
        $content->setState(WorkflowInterface::STATE_PUBLISHED);
        $this->cms->saveEntity($content, true);
    }

    /**
     * Sets a workflow state to the content
     * @param Model\Content $content
     * @param string $state
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setState(Model\Content $content, $state)
    {
        $document   = $this->getContentDocument($content);
        $workflow   = $this->getWorkflow($document);
        $states     = $workflow->getAllStates();
        if (!in_array($state, $states)) {
            throw new Exception\InvalidArgumentException(
                sprintf('%s: Unknown state value; Available: %s', __METHOD__, implode(', ', $states)));
        }
        //TODO - authorization
        if (true /* uzivatel ma pravo na change*/) {

        }
        if ($state == WorkflowInterface::STATE_PUBLISHED) {
            $this->publishContent($content);
        } else {
            $content->setState($state);
            $this->cms->saveEntity($content);
        }
    }

    /**
     * Returns document for the given content
     * @param Model\Content $content
     * @return Model\Document
     */
    public function getContentDocument(Model\Content $content)
    {
        $path = $content->getPath();
        $components = $this->pathBuilder->getStoragePathComponents($path);
        array_pop($components);
        array_pop($components);
        $docPath    = $this->pathBuilder->buildStoragePath($components, true);
        $document = $this->repository->getEntity($docPath);
        if ($document instanceof Model\Document) {
            return $document;
        }
        return null;
    }

    public function addDocumentContent(Model\Document $document, Model\Content $content, $index = 0)
    {
        $path           = $document->getPath();
        $version        = count($this->getDocumentContents($document, $index));
        $components     = array($path, 'Contents' . $index, $version);
        $contentPath    = $this->pathBuilder->buildStoragePath($components, true);
        $content->setPath($contentPath);
        $content->setState(WorkflowInterface::STATE_NEW);
        $this->cms->saveEntity($content);
    }

    /**
     * @param Model\Document $document
     * @param int $index
     * @param int $version
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return Model\Content
     */
    public function getDocumentContent(Model\Document $document, $index, $version/*, $state {PUBLISHED}*/)
    {
        if (!is_integer($version)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Argument %d passed to %s must be an type of %s, %s given',
                    2, __METHOD__, 'integer', gettype($version)));
        }
        if (!is_integer($index)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Argument %d passed to %s must be an type of %s, %s given',
                    3, __METHOD__, 'integer', gettype($index)));
        }
        $components = array(
            $document->getPath(),
            'Contents.',
            $index,
            $version,
        );
        $path   = $this->pathBuilder->buildStoragePath($components, true);
        $entity = $this->repository->getEntity($path);
        return $entity;
    }

    /**
     * @param Model\Document $document
     * @return array <\Vivo\CMS\Model\ContentContainer>
     */
    public function getContentContainers(Model\Document $document)
    {
        $containers = $this->repository->getChildren($document, 'Vivo\CMS\Model\ContentContainer');

        uasort($containers, function($a, $b) { /* @var $a \Vivo\CMS\Model\ContentContainer */
            return $a->getOrder() > $b->getOrder();
        });

        return $containers;
    }

    /**
     * @param Model\Document $document
     * @param int $index
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return array
     */
    public function getDocumentContents(Model\Document $document, $index/*, $state {PUBLISHED}*/)
    {
        if (!is_integer($index)) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Argument %d passed to %s must be an type of integer, %s given',
                    2, __METHOD__, gettype($index)));
        }
        $pathElements   = array($document->getPath(), 'Contents.', $index);
        $path           = $this->pathBuilder->buildStoragePath($pathElements, true);
        return $this->repository->getChildren(new Model\Entity($path));
    }

    /**
     * @param Model\Document $document
     * @param string $target Path.
     */
    public function moveDocument(Model\Document $document, $target)
    {
        $this->repository->moveEntity($document, $target);
        $this->repository->commit();
    }

    /**
     * @param Model\Document $document
     * @return Model\Document
     */
    public function saveDocument(Model\Document $document)
    {
        $options = array(
            'published_content_types' => $this->getPublishedContentTypes($document),
        );
        $this->cms->saveEntity($document, $options);

        return $document;
    }

    /**
     * @param Model\ContentContainer $container
     * @param Model\Content $content
     * @return \Model\Content
     */
    public function createContent(Model\ContentContainer $container, Model\Content $content)
    {
        $versions = count($this->getContentVersions($container));

        $path = $container->getPath().'/'.$versions;

        $content->setPath($path);

        $this->updateContentStates($container, $content);
        $content = $this->cms->prepareEntityForSaving($content);

        $this->repository->saveEntity($content);
        $this->repository->commit();

        return $content;
    }

    /**
     * Saves content
     * The entity is prepared before saving into repository
     *
     * @param Model\Content $content
     * @return Model\Content
     */
    public function saveContent(Model\Content $content)
    {
        $container = $this->cms->getEntityParent($content);

        $this->updateContentStates($container, $content);

        $content = $this->cms->prepareEntityForSaving($content);

        $this->repository->saveEntity($content);
        $this->repository->commit();

        return $content;
    }

    /**
     * @param Model\ContentContainer $container
     * @param Model\Content $content
     */
    protected function updateContentStates(Model\ContentContainer $container, Model\Content $content)
    {
        $contentVersions = $this->getContentVersions($container);

        foreach ($contentVersions as $version) { /* @var $version \Vivo\CMS\Model\Content */
            if($version->getUuid() !== $content->getUuid()
            && $version->getState() == WorkflowInterface::STATE_PUBLISHED)
            {
                $version->setState(WorkflowInterface::STATE_ARCHIVED);

                $this->cms->saveEntity($version, false);
            }
        }
    }

    /**
     * Returns child documents.
     * @param Model\Folder $document
     * @return Model\Folder[]
     */
    public function getChildDocuments(Model\Folder $document)
    {
        $children   = $this->repository->getChildren($document);
        $result = array();
        foreach ($children as $child) {
            if ($child instanceof Model\Folder) {
                $result[] = $child;
            }
        }
        return $result;
    }

    public function getAllStates(Model\Document $document)
    {

    }

    public function getAvailableStates(Model\Document $document)
    {

    }

    /**
     * @deprecated Use self::getContentVersions
     *
     * @see \Vivo\CMS\Api\DocumentInterface::getContents()
     */
    public function getContents(Model\ContentContainer $container)
    {
        return $this->getContentVersions($container);
    }

    /**
     * @param Model\ContentContainer $container
     * @return array <\Vivo\CMS\Model\Content>
     */
    public function getContentVersions(Model\ContentContainer $container)
    {
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        return $contents;
    }

    /**
     * @param Model\Document $document
     * @return \Vivo\CMS\Workflow\WorkflowInterface
     */
    public function getWorkflow(Model\Document $document)
    {
        $workflow   = $this->workflowFactory->get($document->getWorkflow());
        return $workflow;
    }
}
