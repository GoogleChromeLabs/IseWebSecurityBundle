<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Ise\WebSecurityBundle\Options\ContextChecker;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * TrustedTypesSubscriber
 * Request subscriber for implementing the Trusted Types CSP policy. This subscriber will work with already defined CSP policies.
 */
class TrustedTypesSubscriber implements EventSubscriberInterface
{
    private $configProvider;
    private $context;
    private $logger;
    private $policyIssueMessage = "Trusted types policy already defined in CSP header in request from %s. This may cause unexpected behaviour.";

    public function __construct(ConfigProviderInterface $configProvider, ContextChecker $context, LoggerInterface $logger)
    {
        $this->configProvider = $configProvider;
        $this->context = $context;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['responseEvent', -512],
            ]
        ];
    }

    public function responseEvent(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        $options = $this->configProvider->getPathConfig($request);
        //Check is trusted types is active, if not then leave handler
        if (!$options['trusted_types']['active']) {
            return;
        }
        //Check if CSP header is set
        $this->context->checkSecure($request, 'Trusted types');
        $headerSet = $response->headers->has("Content-Security-Policy");
        //If CSP header is set, pull it and append a ';' separator, else set an empty prefix.
        $headerPrefix = $headerSet ? $response->headers->get("Content-Security-Policy").';' : '';
        //Check if trusted types policy is set. If so, print unexpected behaviour error
        if (strpos($headerPrefix, 'trusted-types')) {
            $policyIssue = sprintf($this->policyIssueMessage, $request->getUri());
            $this->logger->log(0, $policyIssue, ['CSP header' => $headerPrefix]);
        }
        
        //Set trusted types CSP policy, and append it to the current policy if one exists
        $response->headers->set("Content-Security-Policy", $this->constructTrustedTypesHeader($options['trusted_types'], $headerPrefix));
    }

    /**
     * constructTrustedTypesHeader method constructs the CSP policy for trusted types. If a CSP policy already exists, the trusted types policy is appended to it.
     *
     * @param Array $options
     * @param String $headerSet
     * @return String
     */
    private function constructTrustedTypesHeader($options, $headerPrefix)
    {
        $policies = "trusted-types ".implode(" ", $options['policies']);
        $requireFor = "require-trusted-types-for ".implode(" ", array_map(function ($value) {
            return sprintf('\'%s\'', $value);
        }, $options['require_for']));
        return sprintf("%s %s; %s;", $headerPrefix, $policies, $requireFor);
    }
}
