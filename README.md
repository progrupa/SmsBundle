Progrupa Sms Bundle
==================
Extensible Symfony2 bundle for sending simple SMS messages.

## Installation
The bundle is available via composer, simply call `composer require progrupa/sms-bundle:dev-master` or add `"progrupa/sms-bundle": "dev-master"` to your composer.json and then call `composer update progrupa/sms-bundle`.

Then enable the pundle in your AppKernel.php:
```
    new Progrupa\SmsBundle\ProgrupaSmsBundle()
```

## Basic configuration
```
# config.yml
progrupa_sms:
    transport: 'smsapi.pl'
```

## Usage

```
    $result = $this->get('progrupa.sms')->send('48111222333', 'Text message');
    if ($result->isSuccess) {
        echo "SMS was sent";
    }
```

## Default provider
The bundle provides support for SmsApi.pl messaging service. To use it, You'll need to provide 
credentials of an active account. This is done through bundle configuration:

```
progrupa_sms:
    smsapi_pl:
        username: YourUsername
        password: YourPassword
        options: 
            from: Awesome Co
            encoding: utf-8
```

The `options`  section allows to specify any options You wish to pass with send action. Full documentation can be found 
on the provider's [website](https://www.smsapi.pl/rest)

## Custom transport
To support your own SMS provider You need to create a TransportInterface class, declare it as a service and tag with `progrupa.sms.transport`. 
Then configure Progrupa Sms Bundle to use Your transport instead of the default one.

Example transport:
```php
use Progrupa\SmsBundle\Model\Result;
use Progrupa\SmsBundle\Model\Sms;
use Progrupa\SmsBundle\Transport\TransportInterface;

class MyAwesomeTransport implements TransportInterface
{
    /**
     * @param Sms $sms Message to be sent
     * @return Result
     */
    public function send(Sms $sms)
    {
        $result = $this->magic->sendSMS($sms->recipient, $sms->message);
        return $result;
    }
}
```
Example service configuration:
```xml
    <service id="foo.my_awesome_transport" class="MyAwesomeTransport">
        <arguments>...</arguments>
        <tag name="progrupa.sms.transport" alias="my_awesome_transport" />
    </service>
```
or
```yml
services:
    foo.my_awesome_transport:
        class: MyAwesomeTransport
        tags:
            - {name: "progrupa.sms.transport", alias: "my_awesome_transport"}
```
Example configuration:
```
# config.yml
progrupa_sms:
    transport: 'my_awesome_transport'
```
