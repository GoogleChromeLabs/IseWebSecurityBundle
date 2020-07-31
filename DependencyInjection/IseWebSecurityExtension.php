<?php
namespace Ise\WebSecurityBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

class IseWebSecurityExtension extends Extension
{
    private const CONFIG_PATH = __DIR__.'/../Resources/config';
    public function load(array $configs, ContainerBuilder $container)
    {
        //!Work in progress as described in Configuration.php To be rebuild in #7 and #3
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $defaults = $config['defaults'];
        
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(self::CONFIG_PATH)
        );
        $loader->load('services.yaml');
        
        $presets = Yaml::parseFile(self::CONFIG_PATH.'/presets.yaml')['presets'];
        
        if (isset($defaults['preset'])) {
            $defaults = array_merge($defaults, $presets[$defaults['preset']]);
        }
        
        $configProvider = $container->getDefinition('ise_config.provider');
        $configProvider->setArgument('$defaults', $defaults);
        $configProvider->setArgument('$paths', $config['paths']);
        $configProvider->setArgument('$presets', $presets);
    }
}
