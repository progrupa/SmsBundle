<?php

namespace Progrupa\SmsBundle\Transport;


use Progrupa\SmsBundle\Exception\UnhandledOption;
use Progrupa\SmsBundle\Model\Result;
use Progrupa\SmsBundle\Model\Sms;
use SMSApi;

class SmsapiPlTransport implements TransportInterface
{
    /** @var  SMSApi\Client */
    private $client;
    /** @var  array */
    private $options;

    public function __construct($login, $password, $options = [])
    {
        $this->client = new SMSApi\Client($login);
        $this->client->setPasswordRaw($password);
        $this->options = $options;
    }

    public function send(Sms $sms)
    {
        $smsapi = new SMSApi\Api\SmsFactory();
        $smsapi->setClient($this->client);

        $result = new Result();

        try {
            $actionSend = $smsapi->actionSend();

            $this->setOptions($actionSend);

            $actionSend->setTo($sms->recipient);
            $actionSend->setText($sms->message);
            $actionSend->setSender($sms->sender);

            $response = $actionSend->execute();

            foreach ($response->getList() as $status) {
                if ($status->getError()) {
                    $result->setSuccess(false);
                    $result->setMessage($status->getError());
                }
            }
        } catch (SMSApi\Exception\SmsapiException $exception) {
            $result->setMessage(false);
            $result->setMessage($exception->getMessage());
        }

        return $result;
    }

    private function setOptions(SMSApi\Api\Action\AbstractAction $action)
    {
        foreach ($this->options as $option => $value) {
            $method_name = 'set' . ucfirst($option);
            if (method_exists($action, $method_name)) {
                call_user_func([$action, $method_name], $value);
            } else {
                throw new UnhandledOption(sprintf("Unhandled option passed: %s", $option));
            }
        }
    }
}
