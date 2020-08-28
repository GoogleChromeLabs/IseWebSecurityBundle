<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Ise\WebSecurityBundle\Options\ContextChecker;

class ResponseSubscriber implements EventSubscriberInterface
{
    private $configProvider;
    private $context;

    public function __construct(ConfigProviderInterface $configProvider, ContextChecker $context)
    {
        $this->configProvider = $configProvider;
        $this->context = $context;
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
            $this->context->checkSecure($request, 'COOP');
            $response->headers->set('Cross-Origin-Opener-Policy', $options['coop']['policy']);
        }

        if ($options['coep']['active']) {
            $this->context->checkSecure($request, 'COEP');
            $response->headers->set('Cross-Origin-Embedder-Policy', $options['coep']['policy']);
        }
        
        $event->setResponse($response);
    }
}
