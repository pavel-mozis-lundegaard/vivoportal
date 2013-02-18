<?php
namespace Vivo\CMS\Api;

use Vivo\CMS\Model;
use Vivo\CMS\Exception;

/**
 * DocumentInterface
 * Document API interface
 */
interface DocumentInterface
{
    /**
     * Returns array of published contents of given document.
     * @param Model\Document $document
     * @return Model\Content[]
     */
    public function getPublishedContents(Model\Document $document);

    /**
     * Returns array of published content types (class names of published contents)
     * If there are no published contents, returns an empty array
     * @param \Vivo\CMS\Model\Document $document
     * @return string[]
     */
    public function getPublishedContentTypes(Model\Document $document);

    /**
     * Finds published content in ContentContainer,
     * @param Model\ContentContainer $container
     * @return Model\Content|boolean
     * @throws Exception\LogicException when there is more than one published content
     */
    public function getPublishedContent(Model\ContentContainer $container);

    /**
     * @param Model\Content $content
     */
    public function publishContent(Model\Content $content);

    /**
     * Sets a workflow state to the content
     * @param Model\Content $content
     * @param string $state
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setState(Model\Content $content, $state);

    /**
     * Returns document for the given content
     * @param Model\Content $content
     * @return Model\Document
     */
    public function getContentDocument(Model\Content $content);

    public function addDocumentContent(Model\Document $document, Model\Content $content, $index = 0);

    /**
     * @param Model\Document $document
     * @param int $index
     * @param int $version
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return Model\Content
     */
    public function getDocumentContent(Model\Document $document, $index, $version/*, $state {PUBLISHED}*/);

    /**
     * @param Model\Document $document
     * @return array <\Vivo\CMS\Model\ContentContainer>
     */
    public function getContentContainers(Model\Document $document);

    /**
     * @param Model\Document $document
     * @param int $index
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     * @return array
     */
    public function getDocumentContents(Model\Document $document, $index/*, $state {PUBLISHED}*/);

    /**
     * @param Model\Document $document
     * @param string $target Path.
     */
    public function moveDocument(Model\Document $document, $target);

    public function saveDocument(Model\Document $document/*, $parent = null*/);

    /**
     * Returns child documents.
     * @param Model\Document $document
     * @return Model\Document[]
     */
    public function getChildDocuments(Model\Folder $document);

    public function getAllStates(Model\Document $document);

    public function getAvailableStates(Model\Document $document);

    /**
     * @deprecated Use self::getContentVersions
     *
     * @param Model\ContentContainer $container
     * @return array <\Vivo\CMS\Model\Content>
     */
    public function getContents(Model\ContentContainer $container);

    /**
     * @param Model\ContentContainer $container
     * @return array <\Vivo\CMS\Model\Content>
     */
    public function getContentVersions(Model\ContentContainer $container);

    /**
     * @param Model\Document $document
     * @return \Vivo\CMS\Workflow\WorkflowInterface
     */
    public function getWorkflow(Model\Document $document);
}
