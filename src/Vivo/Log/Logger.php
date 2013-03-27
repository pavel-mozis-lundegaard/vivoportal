<?php
namespace Vivo\Log;

class Logger extends \Zend\Log\Logger
{
    protected $start;

    public function __construct()
    {
        $this->start = microtime(true);
        $this->loggerId = substr(md5(microtime()),0,4);
        parent::__construct();
    }

    public function setStart($start)
    {
        if (is_float($start)) {
            $this->start = $start;
        }
    }

    public function log($priority, $message, $extra = array())
    {
        $extra['deltaTime'] = round((microtime(true) - $this->start)*1000,2);
        $extra['request'] = $this->loggerId;
        parent::log($priority, $message, $extra);
    }
}