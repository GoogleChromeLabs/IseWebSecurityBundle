<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
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
        //!WIP, to be broken out into policy handler class
        $response = $event->getResponse();
        $request = $event->getRequest();

        $options = $this->configProvider->getPathConfig($request);

        if ($options['coop']['active']) {
            $response->headers->set('Cross-Origin-Opener-Policy', $options['coop']['policy']);
        }

        if ($options['coep']['active']) {
            $response->headers->set('Cross-Origin-Embedder-Policy', $options['coep']['policy']);
        }
        
        $event->setResponse($response);
    }
}
