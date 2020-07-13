<?php
// src/Acme/TestBundle/AcmeTestBundle.php
namespace Ise\WebSecurityBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IseWebSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container): void 
    {
        parent::build($container);
    }
}
