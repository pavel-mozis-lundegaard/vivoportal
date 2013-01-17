<?php
namespace Vivo\View\Helper;

use Vivo\Util\Path\PathParser;

use Vivo\CMS\Api\CMS;
use Vivo\CMS\Model\Entity;
use Vivo\UI\Component;
use Vivo\View\Helper\Exception\InvalidArgumentException;

use Zend\Mvc\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\Url;

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

    /**
     * @param Url $urlhelper
     */
    public function __construct(CMS $cms, $options = array())
    {
        $this->cms = $cms;
        $this->options  = array_merge($this->options, $options);
    }

    public function setParser(PathParser $parser) {
        $this->parser = $parser;
    }

    public function __invoke($resourcePath, $source)
    {
        if ($this->options['check_resource'] == true) {
            $this->checkResource($resourcePath, $source);
        }
        $urlHelper = $this->view->plugin('url');

        if ($source instanceof Entity) {
            $entityUrl = $this->cms->getEntityUrl($source);
            $url = $urlHelper('vivo/resource_entity',
                            array('path' => $resourcePath,
                                    'entity' => $entityUrl,
                                    ));
        } elseif (is_string($source)) {
            $url = $urlHelper('vivo/resource',
                            array('source' => $source, 'path' => $resourcePath, 'type' => 'resource'));
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
