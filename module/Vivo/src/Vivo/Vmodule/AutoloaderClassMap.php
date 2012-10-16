<?php
namespace Vivo\Vmodule;

use Zend\Loader\ClassMapAutoloader;

/**
 * AutoloaderClassMap
 * @author david.lukas
 */
class AutoloaderClassMap extends ClassMapAutoloader
{
    /**
     * Config option
     * @var string
     */
    const STREAM_NAME   = 'stream_name';

    /**
     * Stream name (protocol) registered for Vmodule source access
     * @var string
     */
    protected $streamName;

    /**
     * Autoload
     * @param  string $class
     * @return void
     */
    public function autoload($class)
    {
        if (isset($this->map[$class])) {
            $fileUrl        = $this->streamName . '://' . $this->map[$class];
            return include $fileUrl;
        }
    }

    /**
     * Configure autoloader
     * Specify "namespaces" and "stream_name" keys
     * @param array|\Traversable $options
     * @return AutoloaderNs
     */
    public function setOptions($options)
    {
        parent::setOptions($options);
        foreach ($options as $key => $value) {
            switch ($key) {
                case self::STREAM_NAME:
                    if ($value) {
                        $this->setStreamName($value);
                    }
                    break;
                default:
                    // ignore
                    break;
            }
        }
        return $this;
    }

    /**
     * Sets stream name to use for Vmodule source access
     * @param string $streamName
     */
    public function setStreamName($streamName)
    {
        $this->streamName   = $streamName;
    }
}