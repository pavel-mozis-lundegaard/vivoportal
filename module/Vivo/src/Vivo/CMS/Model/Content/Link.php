<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * Class Link is the same as the Linux symlink.
 * Returns document by saved URL and displays it on the current URL.
 * Link hasn't UI component. Link is handled in ComponentFactory.
 */
class Link extends Model\Content
{

    /**
     * @var string Document relPath
     */
    public $relPath;

    /**
     * Constructor.
     * @param string $path Entity path
     */
    public function __construct($path = null)
    {
        parent::__construct($path);
    }

    /**
     * Returns linked document relative path.
     * @return string
     */
    public function getRelPath()
    {
        return $this->relPath;
    }

    /**
     * Sets relative path of linked document.
     * @param type $relPath
     */
    public function setRelPath($relPath) {
        $this->relPath = $relPath;
    }
}
