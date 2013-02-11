<?php
namespace Vivo\View\Helper;

use Zend\Mvc\Router\RouteMatch;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Entity;
use Vivo\View\Helper\Exception\InvalidArgumentException;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper for gettting resource url.
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
     * @var CMS
     */
    private $cms;

    protected $routePrefix;

    /**
     * Constructor.
     * @param CMS $cms
     * @param array $options
     */
    public function __construct(CMS $cms, $options = array())
    {
        $this->cms = $cms;
        $this->options  = array_merge($this->options, $options);
    }

    public function setRoutePrefix($prefix)
    {
        $this->routePrefix = $prefix;
    }

    public function __invoke($resourcePath, $source)
    {


        if ($this->options['check_resource'] == true) {
            $this->checkResource($resourcePath, $source);
        }
        $urlHelper = $this->view->plugin('url');

        if ($source instanceof Entity) {
            $entityUrl = $this->cms->getEntityUrl($source);
            $url = $urlHelper($this->routePrefix . '/resource_entity',
                            array('path' => $resourcePath,
                                    'entity' => $entityUrl,
                                    ));
        } elseif (is_string($source)) {
            $url = $urlHelper($this->routePrefix . '/resource',
                            array('source' => $source, 'path' => $resourcePath, 'type' => 'resource'), true);
        } else {
            throw new InvalidArgumentException(
                    sprintf("%s: Invalid value for parameter 'source'.",
                            __METHOD__), $code, $previous);
        }

        //Replace encoded slashes in the url. It's needed because apache returns 404 when the url contains encoded slashes
        //This behvaior could be changed in apache config, but it is not possible to do that in .htaccess context.
        //@see http://httpd.apache.org/docs/current/mod/core.html#allowencodedslashes
        $url = str_replace('%2F', '/', $url);

        return $url;
    }

    public function checkResource($resourcePath, $source)
    {
        //TODO check resoure and throw exception if doesn't exist.
    }
}
