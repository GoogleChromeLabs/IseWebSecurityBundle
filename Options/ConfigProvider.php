<?php

namespace Ise\WebSecurityBundle\Options;

use Symfony\Component\HttpFoundation\Request;

/**
 * ConfigurationProvider
 * Class implements the ConfigProviderInterface, parses paths and defaults config provided by the container to provide configuration for the current request route.
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Paths configuration, populated via container injection
     *
     * @var [mixed]
     */
    private $paths;
    /**
     * Defaults configuration, merged with per path config to ensure defaults are overwritten
     *
     * @var [mixed]
     */
    private $defaults;

    private $presets;

    public function __construct($defaults = [], $paths = [], $presets = [])
    {
        $this->defaults = $defaults;
        $this->paths = $paths;
        $this->presets = $presets;
    }
    /**
     * getPathConfig parses the request uri and attempt to match it against a path config, where the path config is used as a regex to match against the uri.
     * If no config is found, the default config is returned.
     * If a config is found, then the default and the path config is merged and returned in $options.
     * Path config is order dependant. IF '^/api' comes before '^/' in the config, then '^/api' will be applied and '^/' disregarded provided the first matches.
     * Path based config will overwrite corresponding config in presets. 
     * @param Request $request The request that is to be configured
     * @return array The Config to be applied to the Request.
     */
    public function getPathConfig(Request $request): array
    {
        $uri = $request->getPathInfo() ?? '/';
        foreach ($this->paths as $pathReg => $options) {
            //Remove NULL's to prevent overwriting with defaults.
            $options = array_filter($options);
            //Check if there is a config that matches the URI

            if (preg_match('{'.$pathReg.'}i', $uri)) {
                if (isset($options['preset'])) {
                    $options = array_merge($this->presets[$options['preset']], $options);
                } else {
                    $options = array_merge($this->defaults, $options);
                }
                return $options;
            }
        }
        //Return the defaults if no path configs are found
        return $this->defaults;
    }
}
