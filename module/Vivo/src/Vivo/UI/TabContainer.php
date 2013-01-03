<?php
namespace Vivo\UI;

/**
 * TabContainer
 */
class TabContainer extends ComponentContainer
{
    /**
     * @var string Component / tab name.
     */
    public $selected = false;
    
    private $view_all = false;

    /**
     * Method for selecting a tab. Call method selected() on selected component.
     * @param strign $name Tab name.
     */
    public function select($name = false)
    {
        $selected_component = $this->components[$name];

        if ($selected_component instanceof TabContainerItemInterface) {
            $selected_component->select();
        }

        return $this->selected = $name;
    }
    
    public function setViewAll($value)
    {
        $this->view_all = $value;
    }

    public function prepareTabs()
    {
        $tabs = array();
        
        foreach($this->components as $name => $component) {
            if ($component instanceOf \Vivo\UI\TabContainerItemInterface) {
                if(!$component->isDisabled()) {
                    $tab = array(
                        'name' => $name,
                        'label' => $component->getLabel()
                    );
                }
            } else {
                $tab = array(
                    'name' => $name,
                    'label' => "($name)"
                );
            }
            $tabs[] = $tab;
        }
        
        return $tabs;
    }
    
    public function view()
    {
        $this->view->tabs = $this->prepareTabs();
        $this->view->components = $keys = array_keys($this->components);
        $this->view->selected = $this->selected ?  : reset($keys);
        $this->view->view_all = $this->view_all;
        return parent::view();
    }

}
