<?php

namespace Ise\WebSecurityBundle\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyProvider;
use Ise\WebSecurityBundle\Policies\FetchMetadataDefaultPolicy;
use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyInterface;

class IseWebSecurityFetchMetadataPolicyProviderTest extends TestCase
{
    public function testEmptyConfig()
    {
        $fetchMetadataPolicyProvider = new FetchMetadataPolicyProvider();
        $pathConfig = [];
        $policy = $fetchMetadataPolicyProvider->getFetchMetadataPolicy($pathConfig);
        $this->assertEquals($policy, new FetchMetadataDefaultPolicy([]));
    }

    public function testEmptyPolicy()
    {
        $fetchMetadataPolicyProvider = new FetchMetadataPolicyProvider();
        $pathConfig = [
            "allowed_origins" => ['/api','/index']
        ];
        $policy = $fetchMetadataPolicyProvider->getFetchMetadataPolicy($pathConfig);
        $this->assertEquals($policy, new FetchMetadataDefaultPolicy(['/api','/index']));
    }

    public function testIncorrectInterface()
    {
        $fetchMetadataPolicyProvider = new FetchMetadataPolicyProvider();
        $this->expectException(InvalidArgumentException::class);

        $pathConfig = [
            "policy" => 'Ise\WebSecurityBundle\Tests\FetchMetadataWrongInterface'
        ];
        $fetchMetadataPolicyProvider->getFetchMetadataPolicy($pathConfig);
    }

    public function testCorrectInterface()
    {
        $fetchMetadataPolicyProvider = new FetchMetadataPolicyProvider();
        $pathConfig = [
            "policy" => 'Ise\WebSecurityBundle\Tests\FetchMetadataDummy'
        ];
        $policy = $fetchMetadataPolicyProvider->getFetchMetadataPolicy($pathConfig);
        $this->assertInstanceOf(FetchMetadataDummy::class, $policy);
    }
}

class FetchMetadataWrongInterface
{
    public function applyPolicy()
    {
    }
}

class FetchMetadataDummy implements FetchMetadataPolicyInterface
{
    public function applyPolicy(Request $request): bool
    {
        return true;
    }
}
