<?php
namespace Vivo\CMS\Model\Content;

use Vivo\CMS\Model;
use Vivo\CMS\Exception;

/**
 * Class Navigation
 * @package Vivo\CMS\Model\Content
 */
class Navigation extends Model\Content implements Model\SymRefDataExchangeInterface
{
    /**
     * Navigation type ROOT
     * Navigation tree starting at explicitly specified root path ($this->root)
     */
    const TYPE_ROOT     = 'type_root';

    /**
     * Navigation type REQUESTED DOC
     * Navigation tree starting at the currently requested document
     */
    const TYPE_RQ_DOC   = 'type_rq_doc';

    /**
     * Navigation type ENUM
     * Explicitly named documents will be included in the navigation container
     */
    const TYPE_ENUM = 'type_enum';

    /**
     * Array of supported navigation types
     * @var array
     */
    protected $supportedTypes   = array(
        self::TYPE_ROOT,
        self::TYPE_RQ_DOC,
        self::TYPE_ENUM,
    );

    /**
     * Selected navigation type
     * @var string
     */
    protected $type;

    /**
     * Root path of an entity which is the navigation root
     * Relevant for the TYPE_ROOT navigation type
     * @var string
     */
    protected $root;

    /**
     * Number of levels scanned from root
     * Null means unlimited
     * Relevant for the TYPE_ROOT, TYPE_RQ_DOC and TYPE_ENUM navigation types
     * @var int
     */
    protected $levels;

    /**
     * Should the root document be included in the navigation container?
     * Relevant for the TYPE_ROOT, TYPE_RQ_DOC navigation types
     * @var bool
     */
    protected $includeRoot  = false;

    /**
     * Array of explicitly enumerated documents to include in the navigation
     * Every document is represented as an array with two elements: 'doc_path' and 'children'
     * The 'children' element may contain similar arrays
     * array(
     *      array(
     *          'doc_path'  => '/path/to/doc1/',
     *          'children'  => array(
     *              ...
     *          ),
     *      ),...
     * )
     * @var array
     */
    protected $enumeratedDocs   = array();

    /**
     * Constructor
     * @param string $path Path to entity; If not set, it will be undefined and can be set later before persisting
     */
    public function __construct($path = null)
    {
        parent::__construct($path);
    }

    /**
     * Sets if root should be included in the navigation
     * @param boolean $includeRoot
     */
    public function setIncludeRoot($includeRoot)
    {
        $this->includeRoot = (bool) $includeRoot;
    }

    /**
     * Returns if root should be included in navigation
     * @return bool
     */
    public function includeRoot()
    {
        return $this->includeRoot;
    }

    /**
     * Alias for includeRoot()
     * @return bool
     */
    public function getIncludeRoot()
    {
        return $this->includeRoot();
    }

    /**
     * Sets number of levels from root included in navigation
     * @param int $levels
     */
    public function setLevels($levels)
    {
        if ($levels === '') {
            $levels = null;
        } else {
            $levels = (int)$levels;
        }
        $this->levels = $levels;
    }

    /**
     * Returns number of levels from root included in navigation
     * @return int
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Sets root path where navigation starts
     * @param string $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Returns root path where the navigation starts
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Sets navigation type
     * @param string $type
     * @throws \Vivo\CMS\Exception\InvalidArgumentException
     */
    public function setType($type)
    {
        if (!in_array($type, $this->supportedTypes)) {
            throw new Exception\InvalidArgumentException(
                sprintf("%s: Unsupported navigation type '%s'", __METHOD__, $type));
        }
        $this->type = $type;
    }

    /**
     * Returns navigation type
     * @return string One of self::TYPE_... constants
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the enumerated documents
     * @param array $enumeratedDocs
     */
    public function setEnumeratedDocs(array $enumeratedDocs)
    {
        $this->enumeratedDocs = $enumeratedDocs;
    }

    /**
     * Returns the enumerated documents
     * @return array
     */
    public function getEnumeratedDocs()
    {
        return $this->enumeratedDocs;
    }



    /**
     * Exchange internal values containing symbolic refs / URLs from provided array
     * @param  array $data
     * @return void
     */
    public function exchangeArraySymRef(array $data)
    {
        //Root
        if (array_key_exists('root', $data)) {
            $this->setRoot($data['root']);
        }
        //Enumerated docs
        if (array_key_exists('enumeratedDocs', $data)) {
            $this->setEnumeratedDocs($data['enumeratedDocs']);
        }
    }

    /**
     * Return an array representation of the object's properties containing symbolic refs / URLs
     * @return array
     */
    public function getArrayCopySymRef()
    {
        $data                   = array();
        $data['root']           = $this->getRoot();
        $data['enumeratedDocs'] = $this->getEnumeratedDocs();
        return $data;
    }
}
