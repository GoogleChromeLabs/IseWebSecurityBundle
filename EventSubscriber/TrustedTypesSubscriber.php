<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
                ['responseEvent', 0],
            ]
        ];
    }

    public function responseEvent(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        $options = $this->configProvider->getPathConfig($request);

        $headerSet = $response->headers->has("Content-Security-Policy") ? ";" : "";
        $response->headers->set("Content-Security-Policy", $this->constructTrustedTypesHeader($options['trusted_types'], $headerSet));
    }

    private function constructTrustedTypesHeader($options, $headerSet)
    {
        $policies = "trusted-types ".implode(" ", $options['policies']);
        $requireFor = "require-trusted-types-for ".implode(" ", array_map(function ($value) {
            return sprintf('\'%s\'', $value);
        }, $options['require_for']));
        return sprintf("%s %s; %s;", $headerSet, $policies, $requireFor);
    }
}
