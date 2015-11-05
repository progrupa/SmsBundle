<?php

namespace Progrupa\SmsBundle;


use Progrupa\SmsBundle\DependencyInjection\Compiler\TransportPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProgrupaSmsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransportPass());
    }

}
