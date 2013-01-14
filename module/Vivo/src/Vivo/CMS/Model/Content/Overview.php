<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;

/**
 * VIVO model represents overview documents by path and other criteria on front-end.
 */
class Overview extends Model\Content
{

    const TYPE_DYNAMIC = 'DYNAMIC';
    const TYPE_STATIC = 'STATIC';

    /**
     * Overview type.
     *
     * @var string see TYPE_DYNAMIC and TYPE_STATIC constants
     */
    protected $overviewType;

    /**
     * @var string Path to a document, which sub-documents of it should be displayed in the overview. If a overview path is not set, it shows sub-documents of the current document, which overview is the content of that document.
     * @example en/news/archive/
     */
    protected $overviewPath;

    /**
     * @var string Fulltext criteria.
     */
    protected $overviewCriteria;

    /**
     * @var string Documents sorting.
     * @see Vivo\CMS\Model\Document::$sorting
     */
    protected $overviewSorting;

    /**
     * @var int A number represent documents count in overview.
     */
    protected $overviewLimit;

    /**
     * @var array items for static overview.
     */
    protected $overviewItems = array();

    /**
     * Setting default values
     *
     * @param string $path Entity path
     */
    public function __construct($path = null)
    {
        parent::__construct($path);
    }

    /**
     * Sets overview type
     *
     * @param string $type Overview type
     **/
    public function setType($type)
    {
        $this->overviewType = $type;
    }

    /**
     * @param array $field_names
     * @return string
     * @todo what is it?
     */
    public function getTextContent($field_names = array())
    {
        return parent::getTextContent(
                array_merge($field_names,
                        array('overview_path', 'overview_items')));
    }

    /**
     * Returns overview path.
     * @return string
     */
    public function getOverviewPath()
    {
        return $this->overviewPath;
    }

    /**
     * Returns array of overview items (for static overview).
     * @return array
     */
    public function getOverviewItems()
    {
        return $this->overviewItems;
    }

    /**
     * Returns overview type (STATIC|DYNAMIC).
     * @return string
     */
    public function getOverviewType()
    {
        return $this->overviewType;
    }
}
