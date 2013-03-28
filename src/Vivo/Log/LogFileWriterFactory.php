<?php
namespace Vivo\Log;

use Zend\Log\Writer\Stream;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

/**
 * CmsFactory
 */
class LogFileWriterFactory implements FactoryInterface
{
    /**
     * Options
     * @var array
     */
    protected $options  = array(
        'log_dir'   => null,
    );

    /**
     * Constructor
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options  = array_merge($this->options, $options);
    }

    /**
     * Create service
     * @param ServiceLocatorInterface $serviceLocator
     * @throws Exception\ConfigException
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (!$this->options['log_dir']) {
            throw new Exception\ConfigException(sprintf("%s: 'log_dir' option not set", __METHOD__));
        }
        $date       = date('Y-m-j');
        $filename   = sprintf('%s/vivo_%s.log', $this->options['log_dir'], $date);
        $writer     = new Stream($filename);
        return $writer;
    }
}
