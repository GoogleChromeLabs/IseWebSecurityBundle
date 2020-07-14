<?php
namespace Ise\WebSecurityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class IseWebSecurityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('ise_security.coop.active', $config['coop']['active']);
        $container->setParameter('ise_security.fetch_metadata.active', $config['fetch_metadata']['active']);
        
	    $loader = new YamlFileLoader(
	    	$container,
	    	new FileLocator(__DIR__.'/../Resources/config')
	    );
        $loader->load('services.yaml');

    }
}
