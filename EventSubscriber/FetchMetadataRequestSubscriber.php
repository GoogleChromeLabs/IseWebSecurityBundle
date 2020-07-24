<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FetchMetadataRequestSubscriber implements EventSubscriberInterface
{
    private $fetchMetadataPolicyProvider;
    private $configProvider;
    public function __construct(FetchMetadataPolicyProvider $fetchMetadataPolicyProvider, ConfigProviderInterface $configProvider)
    {
        $this->fetchMetadataPolicyProvider = $fetchMetadataPolicyProvider;
        $this->configProvider = $configProvider;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['requestEvent', 0],
            ]
        ];
    }

    public function requestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $options = $this->configProvider->getPathConfig($request);
        $fetchMetadataPolicy = $this->fetchMetadataPolicyProvider->getFetchMetadataPolicy($options['fetch_metadata']);

        if ($options['fetch_metadata']['active']) {
            if (!$fetchMetadataPolicy->applyPolicy($request)) {
                $response = new Response('', Response::HTTP_FORBIDDEN);
                $response->headers->set('Vary', 'sec-fetch-site, sec-fetch-dest, sec-fetch-mode');
                $event->setResponse($response);
            }
        }
    }
}
