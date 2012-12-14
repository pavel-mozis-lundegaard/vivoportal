<?php
namespace Vivo\CMS\UI\Manager\Explorer;

use Vivo\CMS\Api\CMS;

use Vivo\UI\ComponentContainer;
use Vivo\CMS\Model;

use Zend\Http\Request;

class Explorer extends ComponentContainer //implements IEntityManager
{
    /**
     * Entity beeing explored.
     * @var \Vivo\CMS\Model\Entity
     */
    private $entity;

    /**
     * Last item selected in ribbon
     * @var Vivo\UI\Ribbon\Item
     */
    private $last_item;

    /**
     * Current component
     * @var Vivo\UI\Component
     */
    private $current;

    private $entityChangeListeners = array();

    public function __construct(Request $request, CMS $cms)
    {

        $this->request = $request;
        $this->cms = $cms;
//         $this->ribbon = new Explorer\Ribbon(
//                 new UI\Ribbon\Tab(__CLASS__ . '\entity',
//                         new UI\Ribbon\Group(__CLASS__ . '\show',
//                                 new RibbonItem($this, 'viewer',
//                                         __CLASS__ . '\Viewer\label', 'show',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'browser',
//                                         __CLASS__ . '\Browser\label', 'browse',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'references',
//                                         __CLASS__ . '\References\label',
//                                         'RedirectMap', RibbonItem::NORMAL,
//                                         array($this, 'invoke'))),
//                         new UI\Ribbon\Group(__CLASS__ . '\edit',
//                                 new RibbonItem($this, 'editor',
//                                         __CLASS__ . '\Editor\label', 'edit',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'contents',
//                                         __CLASS__ . '\Contents\label',
//                                         'Overview', RibbonItem::NORMAL,
//                                         array($this, 'invoke'))),
//                         new UI\Ribbon\Group(__CLASS__ . '\structure',
//                                 new RibbonItem($this, 'creator',
//                                         __CLASS__ . '\Creator\label', 'create',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'copy',
//                                         __CLASS__ . '\Transform\label_copy',
//                                         'copy', RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'move',
//                                         __CLASS__ . '\Transform\label_move',
//                                         'move-rename', RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'delete',
//                                         __CLASS__ . '\Delete\label', 'delete',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke')))),
//                 new UI\Ribbon\Tab(__CLASS__ . '\advanced',
//                         new UI\Ribbon\Group(__CLASS__ . '\secure',
//                                 new RibbonItem($this, 'security',
//                                         __CLASS__ . '\Secure\label',
//                                         'zabezpeceni', RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'audit',
//                                         __CLASS__ . '\Audit\label', 'show',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke'))),
//                         new UI\Ribbon\Group(__CLASS__ . '\expert',
//                                 new RibbonItem($this, 'reindex',
//                                         __CLASS__ . '\Reindex\label', 'edit',
//                                         RibbonItem::NORMAL,
//                                         array($this, 'invoke')),
//                                 new RibbonItem($this, 'inspector',
//                                         __CLASS__ . '\Inspect\label',
//                                         'inspect', RibbonItem::NORMAL,
//                                         array($this, 'invoke')))));
        $this->viewer = new Viewer;
        $this->browser = new Browser;
//         $this->editor = $this
//                 ->addEntityChangeListener(new Explorer\Editor($this));
//         $this->security = new Explorer\Secure;
//         $this->contents = new Explorer\Contents;
//         $this->references = new Explorer\References;
//         $this->audit = new Explorer\Audit;
//         $this->inspector = new Explorer\Inspector;
//         $this->reindex = new Explorer\Reindex;
//         $this->creator = new Explorer\Creator;
//         $this->copy = new Explorer\Transform(Explorer\Transform::MODE_COPY);
//         $this->move = new Explorer\Transform(Explorer\Transform::MODE_MOVE);
//         $this->delete = new Explorer\Delete;
    }

    public function __set($name, $value)
    {
        if ($name == 'current')
            die($name);
        parent::__set($name, $value);
    }

    function init()
    {
        if ($relPath = $this->request->getQuery('url', false)) {
            $entity = $this->cms->getSiteEntity($relPath, $this->site);
        } else {
            $entity = $this->cms->getSiteEntity('', $this->site);
        }

//         if ($url = Context::$instance->parameters['url'])
//             $this
//                     ->setEntity(
//                             Context::$instance->site->getEntity('ROOT' . $url),
//                             Context::$instance->parameters['item']);
//         if (!$this->entity) {
//             $this->setEntity(Context::$instance->site->getEntity('ROOT'));
//         }
        parent::init();
    }

    /**
     * This method handles selection of any ribbon item
     * @param Vivo\RibbonItem $item
     */
    function invoke($item)
    {
        if ($item->name) {
            $this->current = $this->{$item->name};
            if ($item->name == 'security')
                $this->current->selected();
            // fix #21928
            if ($item->name == 'creator')
                $this->current->create();
            if ($this->last_item)
                $this->last_item->setActive(false);
            $this->last_item = $item;
            $item->setActive(true);
        }
    }

    function view()
    {
?>
		<div class="ribbon-holder">
			<?$this->ribbon->view(UI\Ribbon::VIEW_ALL) ?>
		</div>
		<div>
		<?
        if ($this->current)
            $this->current->view();
        ?>
		</div>
		<?
    }

    function addEntityChangeListener(IEntityChangeListener $listener)
    {
        return ($this->entityChangeListeners[] = $listener);
    }

    /**
     * @return Vivo\CMS\Model\Entity
     */
    function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Vivo\CMS\Model\Entity $entity
     */
    function setEntity(\Vivo\CMS\Model\Entity $entity, $item_name = false)
    {
        $this->entity = $entity;
        foreach ($this->entityChangeListeners as $listener)
            $listener->onEntityChange($this->entity);
        if (!Context::$instance->async) {
            $this
                    ->setItem(
                            $item_name ? $item_name
                                    : (($this->last_item
                                            && in_array(
                                                    $this->last_item->name,
                                                    array('editor', 'browser',
                                                            'viewer'))) ? $this
                                                    ->last_item->name : 'editor'));
            $this->ribbon->tab1->label = get_class($entity);
        }
    }

    function setItem($name)
    {
        if ($name == 'viewer') {
            if ($this->entity instanceof Model\Document) {
                $this->ribbon->select('tab1');
                $this->invoke($this->ribbon->tab1->group1->viewer);
            } else {
                $name = 'browser';
            }
        }
        if ($name == 'editor') {
            $this->ribbon->select('tab1');
            $this->invoke($this->ribbon->tab1->group2->editor);
        }
        if ($name == 'browser') {
            $this->ribbon->select('tab1');
            $this->invoke($this->ribbon->tab1->group1->browser);
        }
    }

}
