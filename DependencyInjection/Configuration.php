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
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->append($this->getPreset())
                    ->append($this->getReportConfig())
                    ->append($this->getCOEP())
                    ->append($this->getCOOP())
                    ->append($this->getFetchmetaData())
                    ->append($this->getTrustedTypes())
                ->end()

                ->arrayNode('paths')
                    ->useAttributeAsKey('path')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->append($this->getPreset())
                        ->append($this->getReportConfig())
                        ->append($this->getCOEP())
                        ->append($this->getCOOP())
                        ->append($this->getFetchmetaData())
                        ->append($this->getTrustedTypes())
                    ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    private function getCOOP(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('coop');
        $node
        ->children()
            ->booleanNode('active')->end()
            ->booleanNode('policy_overwrite')->end()
            ->scalarNode('policy')->end()
        ->end();
        return $node;
    }

    private function getCOEP(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('coep');
        $node
        ->children()
            ->booleanNode('active')->end()
            ->booleanNode('policy_overwrite')->end()
            ->scalarNode('policy')->end()
        ->end();
        return $node;
    }

    private function getFetchmetaData()
    {
        $node = new ArrayNodeDefinition('fetch_metadata');
        $node->children()
            ->booleanNode('active')->end()
            ->scalarNode('policy')->end()
            ->arrayNode('allowed_endpoints')->prototype('scalar')->end()
        ->end();
        return $node;
    }

    private function getTrustedTypes()
    {
        $node = new ArrayNodeDefinition('trusted_types');
        $node->children()
            ->booleanNode('active')->end()
            ->arrayNode('policies')->prototype('scalar')->end()->end()
            ->arrayNode('require_for')->prototype('scalar')->end()
        ->end();
        return $node;
    }

    private function getReportConfig()
    {
        $node = new ScalarNodeDefinition('report_uri');
        return $node;
    }

    private function getPreset()
    {
        $node = new ScalarNodeDefinition('preset');
        return $node;
    }
}
