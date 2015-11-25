<?php

namespace Progrupa\SmsBundle\Model;


class Sms
{
    /** @var  string Recipient number */
    public $recipient;
    /** @var  string Message to send */
    public $message;
    /** @var  array Optional parameters to be passed along with the message */
    public $options = [];

    public function __construct($recipient = '', $message = '', $options = [])
    {
        $this->recipient = $recipient;
        $this->message = $message;
        $this->options = $options;
    }
}
