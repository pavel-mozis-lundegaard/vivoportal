<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Model;
use Vivo\Repository\RepositoryInterface;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\CMS\Workflow\Factory as WorkflowFactory;
use Vivo\CMS\Workflow\WorkflowInterface;

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

    public function __construct(RepositoryInterface $repository, PathBuilderInterface $pathBuilder,
                                WorkflowFactory $workflowFactory)
    {
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
        $contents = array();
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
     * @return Model\Content|false
     * @throws Exception\LogicException when there are more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container)
    {
        $result = array();
        $contents = $this->repository->getChildren($container, 'Vivo\CMS\Model\Content');
        foreach ($contents as $content) {
            /* @var $content Model\Content */
            if ($content->getState() == Workflow\Basic::STATE_PUBLISHED) {
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
            $oldContent->setState(Workflow\AbstractWorkflow::STATE_ARCHIVED);
            $this->saveEntity($oldContent, false);
        }
        $content->setState(Workflow\AbstractWorkflow::STATE_PUBLISHED);
        $this->saveEntity($content, true);
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
        if ($state == Workflow\AbstractWorkflow::STATE_PUBLISHED) {
            $this->publishContent($content);
        } else {
            $content->setState($state);
            $this->saveEntity($content);
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
        $content->setState(Workflow\AbstractWorkflow::STATE_NEW);
        $this->saveEntity($content);
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

    public function removeDocument(Model\Document $document)
    {
        $this->removeEntity($document);
    }

    public function saveDocument(Model\Document $document/*, $parent = null*/)
    {
        /*
                if($parent != null && !$parent instanceof Model\Document && !$parent instanceof Model\Site) {
                    throw new \InvalidArgumentException(sprintf('Argument %d passed to %s must be an instance of %s',
                        2, __METHOD__, implode(', ', array('Vivo\Model\Document', 'Vivo\Model\Site')))
                    );
                }
         */
        $options    = array(
            'published_content_types'   => $this->getPublishedContentTypes($document),
        );
        $this->saveEntity($document, $options);
    }

    /**
     * Returns child documents.
     * @param Model\Document $document
     * @return Model\Document[]
     */
    public function getChildDocuments(Model\Document $document)
    {
        $children   = $this->repository->getChildren($document);
        $result = array();
        foreach ($children as $child) {
            if ($child instanceof Model\Document) {
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
     * @param Model\ContentContainer $container
     * @return array <\Vivo\CMS\Model\Content>
     */
    public function getContents(Model\ContentContainer $container)
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
        return Workflow\Factory::get($document->getWorkflow());
    }



}
