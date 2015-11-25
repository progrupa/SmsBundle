<?php

namespace Progrupa\SmsBundle\Transport;


use Progrupa\SmsBundle\Exception\UnhandledOption;
use Progrupa\SmsBundle\Model\Result;
use Progrupa\SmsBundle\Model\Sms;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use SMSApi;

class SmsapiPlTransport implements TransportInterface, LoggerAwareInterface
{
    /** @var  SMSApi\Api\SmsFactory */
    private $factory;
    /** @var  array */
    private $options;
    /** @var  LoggerInterface */
    private $logger;

    public function __construct(SMSApi\Api\SmsFactory $factory, SMSApi\Client $client, $options = [])
    {
        $this->factory = $factory;
        $this->factory->setClient($client);
        $this->options = $options;
    }

    public function send(Sms $sms)
    {
        $result = new Result();

        try {
            $actionSend = $this->factory->actionSend();

            $this->setOptions($actionSend, array_merge($this->options, $sms->options));

            $actionSend->setTo($sms->recipient);
            $actionSend->setText($sms->message);

            $response = $actionSend->execute();

            if (isset($this->options['test']) && $this->options['test'] && $this->logger) {
                $this->logger->info(sprintf("Sending sms to %s: '%s'", $sms->recipient, $sms->message));
            }

            if ($response instanceof SMSApi\Api\Response\StatusResponse) {
                foreach ($response->getList() as $status) {
                    if ($status->getError()) {
                        $result->setSuccess(false);
                        $result->setMessage($status->getError());
                    }
                }
            } elseif ($response instanceof SMSApi\Api\Response\ErrorResponse) {
                $result->setSuccess(false);
                $result->setMessage($response->message);
            } else {
                $result->setSuccess(false);
                $result->setMessage("Unrecognized response");
            }
        } catch (SMSApi\Exception\SmsapiException $exception) {
            $result->setMessage(false);
            $result->setMessage($exception->getMessage());
        }

        return $result;
    }

    private function setOptions(SMSApi\Api\Action\AbstractAction $action, $options)
    {
        foreach ($options as $option => $value) {
            $method_name = 'set' . ucfirst($option);
            if (method_exists($action, $method_name)) {
                call_user_func([$action, $method_name], $value);
            } else {
                throw new UnhandledOption(sprintf("Unhandled option passed: %s", $option));
            }
        }
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
