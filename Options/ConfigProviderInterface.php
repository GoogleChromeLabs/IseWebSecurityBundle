<?php

namespace Ise\WebSecurityBundle\Options;

use Symfony\Component\HttpFoundation\Request;

/**
 * ConfigProviderInterface for creating and defining Configuration providers.
 */
interface ConfigProviderInterface
{
    public function getPathConfig(Request $request): array;
}
