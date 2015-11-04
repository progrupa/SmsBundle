<?php

namespace Progrupa\SmsBundle\Model;


class Sms
{
    /** @var  string Recipient number */
    public $recipient;
    /** @var  string Message to send */
    public $message;
    /** @var  string Sender name */
    public $sender;
}
