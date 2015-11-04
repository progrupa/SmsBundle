<?php

namespace Progrupa\SmsBundle\Transport;


use Progrupa\SmsBundle\Model\Result;
use Progrupa\SmsBundle\Model\Sms;

interface TransportInterface
{
    /**
     * @param Sms $sms
     * @return Result
     */
    public function send(Sms $sms);
}
