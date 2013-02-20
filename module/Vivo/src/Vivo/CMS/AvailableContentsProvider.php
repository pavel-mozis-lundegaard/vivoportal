<?php
namespace Vivo\CMS;

/**
 * Class is responsible for giving document
 */
class AvailableContentsProvider
{

    protected $config;

    protected $contents = array();

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Returns name of content classes availbale for given document.
     * @param \Vivo\CMS\Model\Document $document
     * @return array Classnames of available content types.
     */
    public function getAvailableContents(Model\Folder $document)
    {
        $this->contents = $this->config['available_contents'];
        $restrictions = $this->config['restrictions'];

        if (isset($restrictions['document_type'][get_class($document)])){
            $this->intersect($restrictions['document_type'][get_class($document)]);
        }

        $role = 'some_user_role'; //TODO get user role
        if (isset($restrictions['user_role'][$role])){
            $this->intersect($restrictions['user_role'][$role]);
        }

        if (isset($restrictions['site'])){
            $this->intersect($restrictions['site']);
        }
        if (isset($restrictions['document_path'])
                && is_array($restrictions['document_path'])){

            foreach ($restrictions['document_path'] as $path => $contents) {
                if(strpos($document->getPath(), $path) !== false) {
                    $this->intersect($contents);
                }
            }
        }
        return $this->contents;
    }

    /**
     *
     * @param type $restriction
     * @return type
     */
    protected function intersect($restriction)
    {
        if (is_array($restriction) && !empty($restriction)){
            $this->contents =  array_intersect($restriction, $this->contents);
        }
    }
}
