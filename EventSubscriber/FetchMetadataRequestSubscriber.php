<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FetchMetadataRequestSubscriber implements EventSubscriberInterface
{
    private $fetchMetadataPolicy;
    public function __construct(FetchMetadataPolicyInterface $fetchMetadataPolicy, ContainerInterface $container)
    {
        $this->active = $container->getParameter('ise_security.fetch_metadata.active');
        $this->fetchMetadataPolicy = $fetchMetadataPolicy;
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
            if($this->active) {
                if(!$this->fetchMetadataPolicy->applyPolicy($request)){
                    $response = new Response('', Response::HTTP_FORBIDDEN);
                    $response->headers->set('Vary', 'sec-fetch-site, sec-fetch-dest, sec-fetch-mode');
                    $event->setResponse($response);
                }
            }
    }
}