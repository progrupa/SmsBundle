<?php

namespace Progrupa\SmsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('progrupa_sms');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('transport')->end()
                ->arrayNode('smsapi_pl')
                    ->children()
                        ->scalarNode('api_token')->end()
                        ->arrayNode('options')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
