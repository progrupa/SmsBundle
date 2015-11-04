<?php

namespace Progrupa\SmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class ProgrupaSmsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('progrupa.sms.transport', $config['transport']);
        $container->setParameter('progrupa.sms.transport.smsapi_pl.username', $config['smsapi_pl']['username']);
        $container->setParameter('progrupa.sms.transport.smsapi_pl.password', $config['smsapi_pl']['password']);
        $container->setParameter('progrupa.sms.transport.smsapi_pl.options', $config['smsapi_pl']['options']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

}
