<?php
/**
 * Created by PhpStorm.
 * User: dominikkasprzak
 * Date: 05/11/15
 * Time: 10:24
 */

namespace Progrupa\SmsBundle\Tests\Transport;


use Progrupa\SmsBundle\Model\Result;
use Progrupa\SmsBundle\Model\Sms;
use Progrupa\SmsBundle\Transport\SmsapiPlTransport;
use SMSApi\Api\Action\Sms\Send;
use SMSApi\Api\Response\ErrorResponse;
use SMSApi\Api\Response\StatusResponse;
use SMSApi\Api\SmsFactory;
use SMSApi\Client;

class SmsapiPlTransportTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_RESPONSE = '{"count":1,"list":[{"id":"1","points":1,"number":"48888999000","submitted_number":"888999000","status":"QUEUE","error":null,"idx":null}]}';
    const ERROR_RESPONSE = '{"invalid_numbers":[{"number":"48565880","submitted_number":"565880","message":"Invalid phone number"}],"error":13,"message":"No correct phone numbers"}';

    /** @test */
    public function should_create_action_and_execute_it()
    {
        $client = \Phake::mock(Client::class);
        $factory = \Phake::mock(SmsFactory::class);
        $sendAction = \Phake::mock(Send::class);
        \Phake::when($sendAction)->execute()->thenReturn(new StatusResponse(self::DEFAULT_RESPONSE));

        $transport = $this->givenTransport($factory, $client, $sendAction);

        $transport->send(new Sms('888999000', 'test'));

        \Phake::verify($factory)->setClient($client);
        \Phake::verify($factory)->actionSend();
        \Phake::verify($sendAction)->execute();
    }

    /** @test */
    public function should_configure_action()
    {
        $sendAction = \Phake::mock(Send::class);
        \Phake::when($sendAction)->execute()->thenReturn(new StatusResponse(self::DEFAULT_RESPONSE));

        $transport = $this->givenTransport(null, null, $sendAction, ['sender' => 'Zenek', 'encoding' => 'xxx']);

        $transport->send(new Sms('888999000', 'test'));

        \Phake::verify($sendAction)->setSender('Zenek');
        \Phake::verify($sendAction)->setEncoding('xxx');
    }

    /** @test */
    public function should_merge_options_from_SMS()
    {
        $sendAction = \Phake::mock(Send::class);
        \Phake::when($sendAction)->execute()->thenReturn(new StatusResponse(self::DEFAULT_RESPONSE));

        $transport = $this->givenTransport(null, null, $sendAction, ['sender' => 'Zenek', 'encoding' => 'xxx']);

        $transport->send(new Sms('888999000', 'test', ['sender' => 'Krzychu', 'test' => 1]));

        \Phake::verify($sendAction)->setSender('Krzychu');
        \Phake::verify($sendAction)->setEncoding('xxx');
        \Phake::verify($sendAction)->setTest(1);
    }

    /**
     * @test
     *@expectedException \Progrupa\SmsBundle\Exception\UnhandledOption
     */
    public function should_throw_exception_on_invalid_option()
    {
        $sendAction = \Phake::mock(Send::class);
        \Phake::when($sendAction)->execute()->thenReturn(new StatusResponse(self::DEFAULT_RESPONSE));

        $transport = $this->givenTransport(null, null, $sendAction, ['opcjaZDupy' => 'sdfsdf']);

        $transport->send(new Sms('888999000', 'test'));
    }

    /** @test */
    public function should_return_result()
    {
        $transport = $this->givenTransport();

        $result = $transport->send(new Sms('888999000', 'test'));

        $this->assertInstanceOf(Result::class, $result);
        $this->assertTrue($result->isSuccess());
    }

    /** @test */
    public function should_return_error_in_result()
    {
        $sendAction = \Phake::mock(Send::class);
        \Phake::when($sendAction)->execute()->thenReturn(new ErrorResponse(self::ERROR_RESPONSE));

        $transport = $this->givenTransport(null, null, $sendAction);

        $result = $transport->send(new Sms('888999000', 'test'));

        $this->assertInstanceOf(Result::class, $result);
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('No correct phone numbers', $result->getMessage());
    }

    /**
     * @param $factory
     * @param $client
     * @return SmsapiPlTransport
     */
    protected function givenTransport($factory = null, $client = null, $sendAction = null, $options = [])
    {
        $client = $client ? : \Phake::mock(Client::class);
        if (! $sendAction) {
            $sendAction = \Phake::mock(Send::class);
            \Phake::when($sendAction)->execute()->thenReturn(new StatusResponse(self::DEFAULT_RESPONSE));
        }
        if (! $factory) {
            $factory = \Phake::mock(SmsFactory::class);
        }
        \Phake::when($factory)->actionSend()->thenReturn($sendAction);

        $transport = new SmsapiPlTransport($factory, $client, $options);

        return $transport;
    }

}
