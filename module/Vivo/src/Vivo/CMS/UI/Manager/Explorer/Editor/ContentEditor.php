<?php
namespace Vivo\CMS\UI\Manager\Explorer\Editor;

use Vivo\CMS\UI\AbstractForm;
use Vivo\CMS\UI\Manager\Form\ContentEditor as ContentEditorForm;

class ContentEditor extends AbstractForm
{
    /**
     * @var array
     */
    private $contents;
    /**
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;
    /**
     * @var \Vivo\Metadata\MetadataManager
     */
    private $metadataManager;

    /**
     * @param \Vivo\Metadata\MetadataManager $metadataManager
     */
    public function __construct(array $contents, $metadataManager)
    {
        $this->contents = $contents;
        $this->metadataManager = $metadataManager;
        $this->autoAddCsrf = false;
    }

    public function init()
    {
        foreach ($this->contents as $content) {
            if($content->getState() == 'PUBLISHED') {
                $this->entity = $content;
                break;
            }
        }

        $this->entity->setCreated(new \DateTime);
        $this->entity->setModified(new \DateTime);

        $this->getForm()->bind($this->entity);

        parent::init();
    }

    protected function doGetForm()
    {
        $metadata = $this->metadataManager->getMetadata(get_class($this->entity));

        $form = new ContentEditorForm('content-'.$this->entity->getUuid(), $this->contents, $metadata);

        return $form;
    }

    public function changeVersion()
    {

    }

    public function save()
    {
        echo __METHOD__."<br>\n";
        $form = $this->getForm();

        if ($form->isValid()) {
            echo $this->entity->getOverviewType()."<br>\n";
        }
        echo "<hr>";
    }
}