<?php
namespace Vivo\View\Helper;

use Vivo\CMS\Model\Entity;
use Vivo\UI\Component;
use Vivo\View\Helper\Exception\InvalidArgumentException;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

/**
 * View helper for gettting resource url
 */
class Resource extends AbstractHelper
{
    /**
     * Helper options
     * @var array
     */
    private $options = array(
            'check_resource' => false, // usefull for debuging sites
            );

    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @param Url $urlhelper
     */
    public function __construct(Url $urlhelper, $options = array())
    {
        $this->urlHelper = $urlhelper;
        $this->options  = array_merge($this->options, $options);
    }

    public function __invoke($resourcePath, $source)
    {
        if ($this->options['check_resource'] == true) {
            $this->checkResource($resourcePath, $source);
        }
        if ($source instanceof Entity) {
            $entityUrl = $this->cms->getEntityUrl($source);
            return $this->urlHelper
                    ->__invoke('vivo/resource_entity',
                            array('path' => $resourcePath,
                                    'entity' => $entityUrl));
        } elseif (is_string($source)) {
            return $this->urlHelper
                    ->__invoke('vivo/resource',
                            array('source' => $source, 'path' => $resourcePath));
        } else {
            throw new InvalidArgumentException(
                    sprintf("%s: Invalid value for parameter 'source'.",
                            __METHOD__), $code, $previous);
        }
    }

    public function checkResource($resourcePath, $source)
    {
        //TODO check resoure and throw exception if doesn't exist.
    }
}
