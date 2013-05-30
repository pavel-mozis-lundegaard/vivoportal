<?php
namespace Vivo\CMS\Model\Content\Fileboard;

use Vivo\CMS\Model\Content;

class Media extends Content\File
{
    /**
     * @var string Media name.
     */
    protected $name;

    /**
     * @var string Media description.
     */
    protected $description;

    /**
     * @var int Order.
     */
    protected $order;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }
}
