<?php

namespace Progrupa\SmsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransportPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $tagName = $container->getParameter('progrupa.sms.transport_tag');
        $transports = [];

        foreach ($container->findTaggedServiceIds($tagName) as $id => $tags) {
            foreach ($tags as $tag) {
                if ( ! isset($tag['alias'])) {
                    throw new \RuntimeException(sprintf('Progrupa/SmsBundle - Each tag named "%s" of service "%s" must have an "alias" attribute.', $tagName, $id));
                }
                $transports[$tag['alias']] = new Reference($id);
           }
        }

        $selectedTransport = $container->getParameter('progrupa.sms.transport');
        if (! isset($transports[$selectedTransport])) {
            throw new \RuntimeException(sprintf('Progrupa/SmsBundle - Transport "%s" not found', $selectedTransport));
        }

        $container->getDefinition('progrupa.sms')->replaceArgument(0, $transports[$selectedTransport]);
    }
}
