<?php
namespace Vivo\Http\Filter;

use Vivo\Http\StreamResponse;
use Vivo\IO\FileInputStream;
use Vivo\IO\FileOutputStream;
use Vivo\IO\InputStreamInterface;
use Vivo\IO\IOUtil;
use Vivo\Util\Images;

use Zend\Http\Request;

/**
 * Output filter for transforming images.
 */
class ImageTransform implements OutputFilterInterface, InputStreamInterface
{

    /**
     *
     * @var InputStreamInterface
     */
    protected $inputStream;

    /**
     * Whether image was already transformed.
     * @var boolean
     */
    protected $transformed = false;

    /**
     * Transform options.
     * @var array
     */
    protected $options = array();

    /**
     *
     * @var Images
     */
    protected $images;

    /**
     * Attaches filter.
     *
     * Filter is used only form response with 'image/*' content-type.
     *
     * @see \Vivo\Http\Filter\OutputFilterInterface::attachFilter()
     */
    public function attachFilter(Request $request, StreamResponse $response)
    {
        if (substr($response->getHeaders()->get('Content-Type')->getFieldValue(), 0, 6) == 'image/') {
            $this->inputStream = $response->getInputStream();
            $response->setInputStream($this);
        }
        $this->options = $this->extractOptions($request, $response);
    }

    /**
     * Extract options from request query string.
     * @param Request $request
     * @return array
     */
    protected function extractOptions(Request $request, \Zend\Http\Response $response)
    {
        $queryKeys = array_keys($request->getQuery()->toArray());
        $allowedKeys = array('size', 'width', 'height',
            'bgcolor', 'quality', 'bw', 'crop', 'radius');

        $foundKeys = array_intersect($allowedKeys, $queryKeys);
        if (empty($foundKeys)) {
            $this->transformed = true;
            return array();
        }

        $options = array();
        foreach ($foundKeys as $key) {
            $options[$key] = $request->getQuery($key);
        }

        $options['outputType'] = $response->getHeaders()->get('Content-type')->getFieldValue();
        return $options;
    }

    /**
     * Inject Images
     * @param Images $images
     */
    public function setImages(Images $images)
    {
        $this->images = $images;
    }

    /**
     *
     * @return Images
     */
    public function getImages()
    {
        if (!$this->images) {
            $this->images = new Images();
        }
        return $this->images;
    }


    /**
     * Reads from stream.
     * @param type $bytes
     * @return string
     */
    public function read($bytes = 1)
    {
        if (!$this->transformed) {
            $this->transform();
        }
        return $this->inputStream->read($bytes);
    }

    /**
     * Transform image.
     *
     * Method need to save an image to tempoarary file, because image functions can't work with Vivo IO stream
     * (they need to rewind file pointer).
     *
     */
    protected function transform()
    {
        $tmpfile = sys_get_temp_dir() . '/transform_input_' . md5(microtime());
        $fo = new FileOutputStream($tmpfile);
        $util = new IOUtil();
        $util->copy($this->inputStream, $fo);
        $fo->close();
        $this->getImages()->resample($tmpfile, $tmpfile, $this->options);
        $this->inputStream = new FileInputStream($tmpfile);
        $this->transformed = true;
    }

}
