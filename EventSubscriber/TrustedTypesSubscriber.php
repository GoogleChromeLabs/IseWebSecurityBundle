<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
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

    public function __construct(ConfigProviderInterface $configProvider)
    {
        $this->configProvider = $configProvider;
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

        //Check if CSP header is set
        $headerSet = $response->headers->has("Content-Security-Policy");
        //If CSP header is set, pull it and append a ';' separator, else set an empty prefix.
        $headerPrefix = $headerSet ? $response->headers->get("Content-Security-Policy").';' : '';
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
    private function constructTrustedTypesHeader($options, $headerSet)
    {
        $policies = "trusted-types ".implode(" ", $options['policies']);
        $requireFor = "require-trusted-types-for ".implode(" ", array_map(function ($value) {
            return sprintf('\'%s\'', $value);
        }, $options['require_for']));
        return sprintf("%s %s; %s;", $headerSet, $policies, $requireFor);
    }
}
