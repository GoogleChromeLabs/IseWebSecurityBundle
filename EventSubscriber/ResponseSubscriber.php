<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    private $active;
    public function __construct(ContainerInterface $container)
    {
        $this->active = $container->getParameter('ise_security.coop.active');
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
        if ($this->active) {
            $response->headers->set("Cross-Origin-Resource-Policy", "same-origin");
            $response->headers->set("Content-Security-Policy-Report-Only", 'default-src');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        }
        $event->setResponse($response);
    }
}
