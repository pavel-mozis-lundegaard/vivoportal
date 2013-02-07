<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * User defined ui component
 */
class Component extends Model\Content implements ProvideFrontComponentInterface
{

    /**
     * @var string Front component FQCN
     */
    protected $frontComponent  = 'Vivo\CMS\UI\Blank';

    /**
     * @param string $path Entity path in CMS repository
     */
    public function __construct($path = null)
    {
        parent::__construct($path);
    }

    /**
     * Returns front component FQCN.
     * @return string
     */
    public function getFrontComponent()
    {
        return $this->frontComponent;
    }
}
