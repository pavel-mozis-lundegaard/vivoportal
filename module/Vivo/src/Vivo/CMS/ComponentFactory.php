<?php
namespace Vivo\CMS;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Exception\Exception;
use Vivo\CMS\Exception\LogicException;
use Vivo\CMS\Model\Content;
use Vivo\CMS\Model\Document;
use Vivo\CMS\Model\Site;
use Vivo\CMS\UI\InjectModelInterface;
use Vivo\CMS\UI\Content\Layout;
use Vivo\CMS\UI\Content\RawComponentInterface;
use Vivo\UI\ComponentContainer;
use Vivo\UI\ComponentInterface;

use Zend\Di\Di;

/**
 * ComponentFactory is responsible for instatniating UI component for CMS documents and resolving its dependencies.
 */
class ComponentFactory
{

    /**
     * @var \Vivo\CMS
     */
    private $cms;

    /**
     * @var \Zend\Di\Di
     */
    private $di;

    /**
     * @var ComponentResolver
     */
    private $resolver;

    /**
     * @param CMS $cms
     * @param Di $di
     */
    public function __construct(Di $di, CMS $cms, Site $site)
    {
        $this->cms = $cms;
        $this->di = $di;
        $this->site = $site;
    }

    /**
     * Returns root UI component for the given document.
     * @param Document $document
     * @return \Vivo\UI\Component
     */
    public function getRootComponent(Document $document)
    {
        $root = $this->di->get('Vivo\CMS\UI\Root');
        $component = $this->getFrontComponent($document);
        if ($component instanceof RawComponentInterface) {
            $root->setMain($component);
        } else {
            $page = $this->di->get('Vivo\UI\Page');
            $page->setMain($component);
            $root->setMain($page);
        }
        return $root;
    }

    /**
     * Return front component for the given document.
     *
     * @param Document $document
     * @param array $options (Disable Layout)
     * @return \Vivo\UI\Component
     */
    public function getFrontComponent(Document $document, $options = array())
    {
        $contents = $this->cms->getPublishedContents($document);

        if (count($contents) > 1) {
            $frontComponent = $this->di->get('Vivo\UI\ComponentContainer');
            $i = 1;
            foreach ($contents as $content) {
                $cc = $this->getContentFrontComponent($content, $document);
                $frontComponent->addComponent($cc, 'content' . $i++);
            }

        } elseif (count($contents) === 1) {
            $frontComponent = $this
                    ->getContentFrontComponent(reset($contents), $document);
        } else {
            throw new Exception(sprintf("%s: Document '%s' hasn't any published content.", __METHOD__, $document->getPath()));
        }

        if ($frontComponent instanceof RawComponentInterface) {
            return $frontComponent;
        }

        if ($layoutPath = $document->getLayout()) {
            $layout = $this->cms->getSiteDocument($layoutPath, $this->site);
            $panels = $this->getDocumentLayoutPanels($document);
            $frontComponent = $this->applyLayout($layout, $frontComponent, $panels);
        }
        return $frontComponent;
    }

    /**
     * Wrap the UI component to Layout.
     * @param Document $layout
     * @param Component $component
     * @return \Vivo\UI\Component
     */
    public function applyLayout(Document $layout, ComponentInterface $component, $panels = array())
    {
        $layoutComponent = $this->getFrontComponent($layout);

        if (!$layoutComponent instanceof Layout) {
            //this is usualy caused when the document hasn't layout content or has more then one content
            throw new LogicException(
                    sprintf(
                            "%s: Front component for layout must be instance of 'Vivo\CMS\UI\Content\Layout', '%s' given.",
                            __METHOD__, get_class($layoutComponent)));
        }

        $layoutComponent->setMain($component);
        $layoutPanels = $layoutComponent->getLayoutPanels();

        //document could override only panels that are defined in layout, other panels are ignored
        //TODO log warning when document tries to set panel that is not defined in layout
        $mergedPanels = array();
        foreach ($layoutPanels as $name => $panel) {
            $parts = explode('#', $name);
            if (count($parts) == 2 && $this->cms->getEntityUrl($layout) == $parts[0]) {
                $name = $parts[1];
            }

            if (isset($layoutPanels[$name])) {
                $mergedPanels[$name] = isset($panels[$name]) ? $panels[$name] : $layoutPanels[$name];
            }
        }

        foreach ($mergedPanels as $name => $path) {
            $panelDocument = $this->cms->getSiteDocument($path, $this->site);
            $layoutComponent->addComponent($this->getFrontComponent($panelDocument), $name);
        }

        $layoutDocumentPanels = $layout->getLayoutPanels();
        $panels = array_merge($layoutDocumentPanels, $panels);

        if ($parentLayout = $this->cms->getParent($layout)) {
            if ($parentLayout instanceof Document) {
                if ($component = $this
                        ->applyLayout($parentLayout, $layoutComponent, $panels)) {
                    $layoutComponent = $component;
                }
            }
        }
        return $layoutComponent;
    }

    /**
     * Returns panels for document and its parents.
     * @param Document $document
     * @todo this should be cached
     */
    public function getDocumentLayoutPanels(Document $document) {
        $panels = array();
        while($document instanceof Document) {
            $panels = array_merge($document->getLayoutPanels(), $panels);
            $document = $this->cms->getParent($document);
        }
        return $panels;
    }

    /**
     * Instantiates front UI component for the given content.
     * @param Content $content
     * @param Document $document
     * @return \Vivo\UI\Component
     */
    public function getContentFrontComponent(Content $content,
            Document $document)
    {
        $className = $this->resolver->resolve($content);
        $component = $this->di->newInstance($className);
        if ($component instanceof InjectModelInterface) {
            //TODO how to properly inject document and content
            $component->setContent($content);
            $component->setDocument($document);
        }
        return $component;
    }

    /**
     * Instantiates editor UI component for the given content.
     * @param Content $content
     * @param Document $document
     * @return \Vivo\UI\Component
     */
    public function getEditorComponent(Content $content, Document $document)
    {
        //TODO implement
    }

    /**
     * @param ComponentResolver $resolver
     */
    public function setResolver(ComponentResolver $resolver)
    {
        $this->resolver = $resolver;
    }

}
