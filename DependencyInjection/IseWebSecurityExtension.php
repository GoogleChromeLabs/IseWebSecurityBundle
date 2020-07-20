<?php
namespace Ise\WebSecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class IseWebSecurityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        //!Work in progress as described in Configuration.php To be rebuild in #7 and #3
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('ise_security.coop.active', $config['coop']);
        $container->setParameter('ise_security.fetch_metadata.active', $config['fetch_metadata']['active']);
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $fetchMetaDataSubscriber = $container->getDefinition("ise_fetch_metadata.subscriber");
        if ($config['fetch_metadata']['fetch_metadata_policy'] !== null) {
            $fetchMetaDataSubscriber->setArgument(1, new Reference($config['fetch_metadata']['fetch_metadata_policy']));
        }

        $fetchMetaDataDefaultPolicy = $container->getDefinition("ise_fetch_metadata.default_policy");
        $fetchMetaDataDefaultPolicy->setArgument(1, $config['fetch_metadata']['allowed_endpoints']);
    }
}
