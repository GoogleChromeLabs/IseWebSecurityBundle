<?php

namespace Ise\WebSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ise_web_security', 'array');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC for symfony/config < 4.2
            $rootNode = $treeBuilder->root('ise_web_security');
        }

        $rootNode
            ->children()
                ->scalarNode('report_uri')->end()
                ->arrayNode('coop')
                    ->children()
                        ->booleanNode('active')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('fetch_metadata')
                    ->children()
                        ->booleanNode('active')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getCOOP(): EnumNodeDefinition {
        $node = new EnumNodeDefinition('coop');
        $node->values(['none', 'cross-origin', 'same-site', 'same-origin', 'same-origin-allow-popups', 'report-only']);
        return $node;
    }

    private function getCOEP(): EnumNodeDefinition {
        $node = new EnumNodeDefinition('coep');
        $node->values(['require-corp', 'report-only']);
        return $node;
    }

    private function getFetchMetaData(): ArrayNodeDefinition {
        $node = new ArrayNodeDefinition('fetch_metadata');
        return $node;
    }
}