<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * Class SiteMap
 * @package Vivo\CMS\Model\Content
 */
class SiteMap extends Model\Content implements Model\SymRefDataExchangeInterface
{
    /**
     * Path of an entity which is the origin for the sitemap tree calculation
     * If null, current document is assumed as origin
     * @var string
     */
    protected $origin = '/';

    /**
     * Show description flag
     * If set to TRUE, page description will be displayed in every node of the tree
     * @var bool
     */
    protected $showDescription = FALSE;

    /**
     * Include root in the sitemap?
     * @var bool
     */
    protected $includeRoot = FALSE;

    /**
     * Returns origin path where the sitemap calculation starts
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Returns 'showDescription' flag
     * @return bool
     */
    public function getShowDescription()
    {
        return $this->showDescription;
    }

    /**
     * Returns include root
     * @return bool
     */
    public function getIncludeRoot()
    {
        return $this->includeRoot;
    }

    /**
     * Sets origin path where sitemap calculation starts
     * @param string $root
     */
    public function setOrigin($root = null)
    {
        if ($root == '') {
            $root = null;
        }
        $this->origin = $root;
    }

    /**
     * Sets 'showDescription' flag
     * @param bool $showDescription
     */
    public function setShowDescription($showDescription)
    {
        $this->showDescription = $showDescription;
    }

    /**
     * Sets include root
     * @param bool $includeRoot
     */
    public function setIncludeRoot($includeRoot)
    {
        $this->includeRoot = $includeRoot;
    }

    /**
     * Exchange internal values containing symbolic refs / URLs from provided array
     * @param  array $data
     * @return void
     */
    public function exchangeArraySymRef(array $data)
    {
        if (array_key_exists('origin', $data)) {
            $this->setOrigin($data['origin']);
        }
    }

    /**
     * Return an array representation of the object's properties containing symbolic refs / URLs
     * @return array
     */
    public function getArrayCopySymRef()
    {
        $data                   = array();
        $data['origin']         = $this->getOrigin();
        return $data;
    }

}
