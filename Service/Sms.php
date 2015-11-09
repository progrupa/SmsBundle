<?php

namespace Progrupa\SmsBundle\Service;


use Progrupa\SmsBundle\Transport\TransportInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Progrupa\SmsBundle\Model;

class Sms implements LoggerAwareInterface
{
    /** @var  LoggerInterface */
    private $logger;
    /** @var  TransportInterface */
    private $transport;

    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    public function send ($recipient, $messageText)
    {
        $message = new Model\Sms();
        $message->recipient = $recipient;
        $message->message = $messageText;

        return $this->transport->send($message);
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
