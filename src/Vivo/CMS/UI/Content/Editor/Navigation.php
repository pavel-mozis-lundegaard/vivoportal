<?php
namespace Vivo\CMS\UI\Content\Editor;

use Vivo\CMS\Api;
use Vivo\CMS\Model;
use Vivo\UI\AbstractForm;
use Vivo\Form\Form;
use Vivo\CMS\Model\Content\Navigation as NavigationModel;

use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;

/**
 * Class Navigation
 * @package Vivo\CMS\UI\Content\Editor
 */
class Navigation extends AbstractForm implements EditorInterface
{
    /**
     * @var \Vivo\CMS\Model\Content\Navigation
     */
    protected $content;

    /**
     * @var \Vivo\CMS\Api\Document
     */
    private $documentApi;

    /**
     * When set to true, CSRF protection will be automatically added to the form
     * Redefine in descendant if necessary
     * @var bool
     */
    protected $autoAddCsrf  = false;

    /**
     * Constructor
     * @param Api\Document $documentApi
     */
    public function __construct(Api\Document $documentApi)
    {
        $this->documentApi = $documentApi;
    }

    public function setContent(Model\Content $content)
    {
        $this->content = $content;
    }

    public function init()
    {
        $this->getForm()->bind($this->content);

        parent::init();
    }

    public function save(Model\ContentContainer $contentContainer)
    {
        if($this->getForm()->isValid()) {
            if($this->content->getUuid()) {
                $this->documentApi->saveContent($this->content);
            }
            else {
                $this->documentApi->createContent($contentContainer, $this->content);
            }
        }
    }

    public function doGetForm()
    {
        $form = new Form('editor-'.$this->content->getUuid());
        $form->setWrapElements(true);
        $form->setHydrator(new ClassMethodsHydrator(false));
        $form->setOptions(array('use_as_base_fieldset' => true));
        //Type
        $form->add(array(
            'name' => 'type',
            'type' => 'Vivo\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    NavigationModel::TYPE_ROOT          => 'Arbitrary root document',
                    NavigationModel::TYPE_RQ_DOC        => 'Requested document',
                    NavigationModel::TYPE_BREADCRUMBS   => 'Breadcrumbs',
                    NavigationModel::TYPE_ENUM          => 'Enumerated documents',
                ),
            ),
        ));
        //Root
        $form->add(array(
            'name'      => 'root',
            'type'      => 'Vivo\Form\Element\Text',
            'options'   => array(
                'label' => 'Root document path',
            ),
        ));
        //Levels
        $form->add(array(
            'name'      => 'levels',
            'type'      => 'Vivo\Form\Element\Text',
            'options'   => array(
                'label' => 'Number of levels',
            ),
        ));
        //Include root
        $form->add(array(
            'name'      => 'includeRoot',
            'type'      => 'Vivo\Form\Element\Checkbox',
            'options'   => array(
                'label' => 'Include root',
            ),
        ));
        return $form;
    }

}
