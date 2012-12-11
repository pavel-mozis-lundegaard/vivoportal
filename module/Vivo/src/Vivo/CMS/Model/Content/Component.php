<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * User defined ui component
 */
class Component extends Model\Content implements ProvideFrontComponentInterface
{

    /**
     * @var string Default front component FQCN.
     */
    static $DEFAULT_FRONT_COMPONENT = 'Vivo\CMS\UI\Blank';

    /**
     * @var string Front component FQCN
     */
    public $frontComponent;

    /**
     * @param string $path Entity path in CMS repository
     */
    function __construct($path = null)
    {
        parent::__construct($path);
        $this->frontComponent = self::$DEFAULT_FRONT_COMPONENT;
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
