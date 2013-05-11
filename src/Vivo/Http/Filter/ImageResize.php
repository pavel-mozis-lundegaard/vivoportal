<?php
namespace Vivo\Http\Filter;

use Vivo\Http\StreamResponse;
use Zend\Http\Request;

/**
 * Output filter for resizing images
 */
class ImageResize implements OutputFilterInterface,  \Vivo\IO\InputStreamInterface
{

    protected $inputStream;

    protected $options = array();

    /**
     *
     * @var \Vivo\Util\Images
     */
    protected $image;

    /**
     * Attach filter
     *
     * Filter is attached only for image/* content-type.
     *
     * @see \Vivo\Http\Filter\OutputFilterInterface::attachFilter()
     */
    public function attachFilter(Request $request, StreamResponse $response)
    {
        if ($response->getHeaders()->get('Content-Type')->getFieldValue() == 'text/html') {
            $this->inputStream = $response->getInputStream();
            $response->setInputStream($this);
        }
        $this->options = $this->extractOptions($request);
    }

    protected function extractOptions(Request $request)
    {
        $options = array(
            'size'      => $request->getQuery('size'),
            'width'     => $request->getQuery('width'),
            'height'    => $request->getQuery('height'),
            'bgcolor'   => $request->getQuery('bgcolor'),
            'quality'   => $request->getQuery('quality'),
            'bw'        => $request->getQuery('bw', false),
            'crop'      => $request->getQuery('crop', false),
            'radius'    => $request->getQuery('radius', 0),
        );
        return $options;
    }

    public function setImage($image)
    {
            $this->image = $image;
    }

    public function read($bytes = 1) {
        $this->inputStream->read($bytes);
    }
}
