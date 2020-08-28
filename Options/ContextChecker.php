<?php

namespace Ise\WebSecurityBundle\Options;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class ContextChecker {
    private $insecureMessage = "Warning: request from %s is an insecure context. %s policy may not be active as it requires a secure context.";
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function checkSecure(Request $request, String $policy) {
        if(!$request->isSecure()){
            $insecureLog = sprintf($this->insecureMessage, $request->getUri(), $policy);
            $this->logger->error($insecureLog);
        }
    }
}