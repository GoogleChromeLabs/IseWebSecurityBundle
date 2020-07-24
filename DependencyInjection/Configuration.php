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
                    ->append($this->getReportConfig())
                    ->append($this->getCOEP())
                    ->append($this->getCOOP())
                    ->append($this->getFetchmetaData())
                ->end()

                ->arrayNode('paths')
                    ->useAttributeAsKey('path')
                    ->normalizeKeys(false)
                    ->prototype('array')
                        ->append($this->getReportConfig())
                        ->append($this->getCOEP())
                        ->append($this->getCOOP())
                        ->append($this->getFetchmetaData())
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
            ->booleanNode('active')->defaultTrue()->end()
            ->booleanNode('policy_overwrite')->defaultFalse()->end()
            ->scalarNode('policy')->defaultValue('same-origin')->end()
        ->end();
        return $node;
    }

    private function getCOEP(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('coep');
        $node
        ->children()
            ->booleanNode('active')->defaultTrue()->end()
            ->booleanNode('policy_overwrite')->defaultFalse()->end()
            ->scalarNode('policy')->defaultValue('require-corp')->end()
        ->end();
        return $node;
    }

    private function getFetchmetaData()
    {
        $node = new ArrayNodeDefinition('fetch_metadata');
        $node->children()
            ->booleanNode('active')->defaultFalse()->end()
            ->scalarNode('policy')->defaultNull()->end()
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
