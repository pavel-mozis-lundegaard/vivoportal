<?php
namespace Vivo\CMS\Model;

use Vivo\CMS\Security;

/**
 * Represents folder in tree.
 */
class Folder extends Entity
{
    /**
     * @var string Folder name.
     */
    protected $title;

    /**
     * @var string Language.
     */
    protected $language;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $allowListing;

    /**
     * @var int Position of the document in layer. This property could be used as sorting option of the document.
     */
    protected $position;

    /**
     * @var string Specifies which criteria will classify sub-documents in the lists (newsletters, sitemap, menu, etc.)
     */
    protected $sorting;

    /**
     * @var string Replication id;
     */
    protected $replicationGroupId;

    /**
     * Absolute last path to entity stored in repository before move to trash.
     * @var string
     */
    protected $lastPath;

    /**
     * @var \Vivo\CMS\Model\Entity\Security
     */
    protected $security;

    /**
     * @param string $path Folder (entity) path in CMS repository.
     * @param \Vivo\CMS\Model\Entity\Security $security
     * @todo default security model
     */
    public function __construct($path = null, $security = null)
    {
        parent::__construct($path);

        $this->security = $security;

//         $security ?
//                 :
//                 //(self::$DEFAULT_SECURITY ? :
//                     new CMS\Model\Entity\Security(
//                         array( /*Security\Manager::ROLE_VISITOR			=> array(Security\Manager::GROUP_ANYONE),
//                                Security\Manager::ROLE_PUBLISHER		=> array(Security\Manager::GROUP_PUBLISHERS),
//                                Security\Manager::ROLE_ADMINISTRATOR	=> array(Security\Manager::GROUP_ADMINISTRATORS)
//                                 */
//                         ));
//         // 		);

    }

    /**
     * Sets folder title.
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns title.
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param bool allowListing
     */
    public function setAllowListing($allowListing = true)
    {
        $this->allowListing = (bool)$allowListing;
    }

    /**
     * @return bool
     */
    public function getAllowListing()
    {
        return $this->allowListing;
    }

    /**
     * @param array $field_names
     * @return string
     */
    public function getTextContent($field_names = array())
    {
        return parent::getTextContent(
                array_merge($field_names, array('description')));
    }
}
