<?php
namespace Vivo\Vmodule;

use Zend\Loader\StandardAutoloader;
use Zend\Loader\Exception\InvalidArgumentException;

/**
 * AutoloaderNs
 * Autoloads Vmodule classes from Storage using registered PSR-0 namespace
 * @author david.lukas
 */
class AutoloaderNs extends StandardAutoloader
{
    const STREAM_NAME   = 'stream_name';

    /**
     * Stream name (protocol) registered for Vmodule source access
     * @var string
     */
    protected $streamName;

    /**
     * Load a class from a namespace
     * @param string $class
     * @param string $type
     * @return bool|string
     * @throws InvalidArgumentException
     */
    protected function loadClass($class, $type)
    {
        if ($type != self::LOAD_NS) {
            throw new InvalidArgumentException(sprintf("%s: Type '%s' not supported.", __METHOD__, $type));
        }
        if (!$this->streamName) {
            throw new InvalidArgumentException(sprintf('%s: Stream name not set.', __METHOD__));
        }
        //Namespace autoloading
        foreach ($this->$type as $leader => $path) {
            if (0 === strpos($class, $leader)) {
                //Trim off leader (namespace i.e. namespace)
                $trimmedClass   = substr($class, strlen($leader));
                //Create filename
                $filename       = $this->transformClassNameToFilename($trimmedClass, $path);
                $fileUrl        = $this->streamName . '://' . $filename;
                return include $fileUrl;
            }
        }
        return false;
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