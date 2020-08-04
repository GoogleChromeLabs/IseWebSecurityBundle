<?php

namespace Ise\WebSecurityBundle\Tests;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Ise\WebSecurityBundle\Options\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class IseWebSecurityConfigProviderTest extends TestCase
{
    protected $defaultOptions = [
        'preset' => null,
        'fetch_metadata' => [
            'active' => true,
            'policy' => null,
            'allowed_origins' => []
        ],
        'coop' => [
            'active' => true,
            'policy' => 'same-origin',
            'policy_overwrite' => false
        ],
        'coep' => [
            'active' => true,
            'policy' => 'require-corp',
            'policy_overwrite' => false
        ]
    ];

    protected $routeMatch = [
        'preset' => null,
        'fetch_metadata' => [
            'active' => false,
            'policy' => null,
            'allowed_origins' => []
        ],
        'coop' => [
            'active' => true,
            'policy' => 'same-origin',
            'policy_overwrite' => false
        ],
        'coep' => [
            'active' => true,
            'policy' => 'require-corp',
            'policy_overwrite' => false
        ]
    ];

    protected $routeExactMatch = [
        'preset' => null,
        'fetch_metadata' => [
            'active' => false,
            'policy' => null,
            'allowed_origins' => []
        ],
        'coop' => [
            'active' => false,
            'policy' => 'same-origin',
            'policy_overwrite' => false
        ],
        'coep' => [
            'active' => true,
            'policy' => 'require-corp',
            'policy_overwrite' => false
        ]
    ];

    protected $routeNoMatch = [
        'preset' => null,
        'fetch_metadata' => [
            'active' => false,
            'policy' => null,
            'allowed_origins' => []
        ],
        'coop' => [
            'active' => false,
            'policy' => 'no match',
            'policy_overwrite' => true
        ],
        'coep' => [
            'active' => false,
            'policy' => 'no match',
            'policy_overwrite' => true
        ]
    ];

    protected $routeOverwrite = [
        'preset' => null,
        'fetch_metadata' => [
            'active' => false,
            'policy' => 'App\A\Random\Thing',
            'allowed_origins' => ['/orign1','/origin2']
        ],
        'coop' => [
            'active' => false,
            'policy' => 'no match',
            'policy_overwrite' => true
        ],
        'coep' => [
            'active' => false,
            'policy' => 'no match',
            'policy_overwrite' => true
        ]
    ];

    protected $fullPreset = [
        'preset' => 'full',
        'fetch_metadata' => [
            'active' => false,
            'policy' => 'App\A\Random\Preset',
            'allowed_origins' => ['/orign1','/origin2']
        ],
        'coop' => [
            'active' => true,
            'policy' => 'preset',
            'policy_overwrite' => true
        ],
        'coep' => [
            'active' => false,
            'policy' => 'preset',
            'policy_overwrite' => true
        ]
    ];

    protected $routeOverwritePreset = [
        'preset' => 'full',
        'fetch_metadata' => [
            'active' => false,
            'policy' => 'App\A\Random\Thing',
            'allowed_origins' => ['/orign1','/origin2']
        ],
        'coop' => [
            'active' => false,
            'policy' => 'no match',
            'policy_overwrite' => true
        ],
        'coep' => [
            'active' => false,
            'policy' => 'no match',
            'policy_overwrite' => true
        ]
    ];

    public function testDefaults(): void
    {
        $provider = $this->getProvider();
        $this->assertEquals(
            $this->defaultOptions,
            $provider->getPathConfig(Request::create('/default/path'))
        );
    }

    public function testRegex(): void
    {
        $provider = $this->getProvider();
        $this->assertEquals(
            $this->routeMatch,
            $provider->getPathConfig(Request::create('/test/regex/hi'))
        );
    }

    public function testExact(): void
    {
        $provider = $this->getProvider();
        $this->assertEquals(
            $this->routeExactMatch,
            $provider->getPathConfig(Request::create('/test/exact'))
        );
    }

    public function testNoMatch(): void
    {
        $provider = $this->getProvider();
        $this->assertEquals(
            $this->defaultOptions,
            $provider->getPathConfig(Request::create('/notMatching'))
        );
    }

    public function testFullOverwrite(): void
    {
        $provider = $this->getProvider();
        $this->assertEquals(
            $this->routeOverwrite,
            $provider->getPathConfig(Request::create('/full/overwrite'))
        );
    }

    public function testPresetOverwrite(): void
    {
        $provider = $this->getProvider();
        $this->assertEquals(
            $this->fullPreset,
            $provider->getPathConfig(Request::create('/full/preset'))
        );
    }

    public function testPathOverwritePreset(): void
    {
        $provider = $this->getProvider();

        $this->assertEquals(
            $this->routeOverwritePreset,
            $provider->getPathConfig(Request::create('/overwrite/preset'))
        );
    }

    public function getProvider(): ConfigProviderInterface
    {
        return new ConfigProvider(
            $this->defaultOptions,
            [
                '/test/exact' => $this->routeExactMatch,
                '^/test/regex' => $this->routeMatch,
                '/no/match' => $this->routeNoMatch,
                '/full/overwrite' => $this->routeOverwrite,
                '/full/preset' => [
                    'preset' => 'full'
                ],
                '/overwrite/preset' => $this->routeOverwritePreset
            ],
            [
                'full' => $this->fullPreset
            ]
        );
    }
}
