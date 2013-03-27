<?php
namespace Vivo\Http;

use Vivo\IO\CloseableInterface;
use Vivo\IO\IOUtil;

use Zend\Mvc\ResponseSender\AbstractResponseSender;
use Zend\Mvc\ResponseSender\SendResponseEvent;

/**
 * Class StreamResponseSender
 * Custom response sender for Vivo StreamResponse
 * @package Vivo\Http
 */
class StreamResponseSender extends AbstractResponseSender
{
    /**
     * Send the stream
     *
     * @param  SendResponseEvent $event
     * @return StreamResponseSender
     */
    public function sendStream(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        /** @var $response StreamResponse */
        if (!$response instanceof StreamResponse) {
            return $this;
        }
        if ($event->contentSent()) {
            return $this;
        }
        $source = $response->getInputStream();
        $target = $response->getOutputStream();
        $util   = new IOUtil();
        $util->copy($source, $target);
        if ($source instanceof CloseableInterface) {
            $source->close();
        }
        if ($target instanceof CloseableInterface) {
            $target->close();
        }
        $event->setContentSent();
        return $this;
    }

    /**
     * Send the response
     * @param SendResponseEvent $event
     * @return $this|void
     */
    public function __invoke(SendResponseEvent $event)
    {
        $response = $event->getResponse();
        if (!$response instanceof StreamResponse) {
            return $this;
        }
        $this->sendHeaders($event);
        $this->sendStream($event);
        $event->stopPropagation(true);
        return $this;
    }
}
