<?php

namespace Ise\WebSecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ise_web_security', 'array');

        $rootNode = $treeBuilder->getRootNode();
        //!Work in progress, config tree to be constructed in Issues #7 and #3
        $rootNode
            ->children()
                ->append($this->getReportConfig())
                ->arrayNode('coop')
                    ->children()
                        ->booleanNode('active')->defaultFalse()->end()
                    ->end()
                ->end()
                ->append($this->getFetchmetaData())
            ->end()
        ;

        return $treeBuilder;
    }

    private function getCOOP(): ScalarNodeDefinition
    {
        $node = new ScalarNodeDefinition('coop');
        $node->defaultValue('same-origin');
        return $node;
    }

    private function getCOEP(): EnumNodeDefinition
    {
        $node = new EnumNodeDefinition('coep');
        $node->defaultValue('require-corp')
            ->values(['require-corp', 'report-only']);
        return $node;
    }

    private function getFetchmetaData()
    {
        $node = new ArrayNodeDefinition('fetch_metadata');
        $node->children()
            ->booleanNode('active')->defaultFalse()->end()
            ->scalarNode('fetch_metadata_policy')->defaultNull()->end()
            ->arrayNode('allowed_endpoints')->prototype('scalar')->defaultValue(array())->end()
        ->end();
        return $node;
    }

    private function getReportConfig()
    {
        $node = new ScalarNodeDefinition('report_uri');
        $node->defaultNull();
        return $node;
    }
}
