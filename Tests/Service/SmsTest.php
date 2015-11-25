<?php

namespace Progrupa\SmsBundle\Tests\Service;


use Progrupa\SmsBundle\Model\Result;
use Progrupa\SmsBundle\Service\Sms;
use Progrupa\SmsBundle\Transport\TransportInterface;

class SmsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function should_use_transport_to_send_message()
    {
        $transport = \Phake::mock(TransportInterface::class);
        $service = new Sms($transport);

        $message = new \Progrupa\SmsBundle\Model\Sms();
        $message->recipient = '555555555';
        $message->message = 'Test message';
        $message->options = ['from' => 'Sender'];

        $service->send('555555555', 'Test message', ['from' => 'Sender']);

        \Phake::verify($transport)->send($message);
    }

    /** @test */
    public function should_return_send_result()
    {
        $transport = \Phake::mock(TransportInterface::class);

        $service = new Sms($transport);

        $result = new Result(true, '');
        \Phake::when($transport)->send(\Phake::anyParameters())->thenReturn($result);
        $this->assertEquals($result, $service->send('555555555', 'Test message', ['from' => 'Sender']));

        $result = new Result(false, 'Error encountered');
        \Phake::when($transport)->send(\Phake::anyParameters())->thenReturn($result);
        $this->assertEquals($result, $service->send('555555555', 'Test message', ['from' => 'Sender']));
    }
}
