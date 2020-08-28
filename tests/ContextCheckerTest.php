<?php

namespace Ise\WebSecurityBundle\Tests;

use Ise\WebSecurityBundle\Options\ContextChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\Logger;

class IseWebSecurityContextCheckerTest extends TestCase
{
    public function testSecureContext()
    {
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $context = new ContextChecker($logger);

        $request = Request::create('https://127.0.0.1');

        $context->checkSecure($request, 'COOP');
        $logger->expects($this->never())
            ->method('error');
    }
}
