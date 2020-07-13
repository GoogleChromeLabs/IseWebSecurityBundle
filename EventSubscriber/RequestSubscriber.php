<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestSubscriber implements EventSubscriberInterface
{
    private $active;
    private $corsEndpoints;
    public function __construct(ContainerInterface $container)
    {
        $this->active = $container->getParameter('ise_security.fetch_metadata.active');
        $this->active = []; //Stub until Config is constructed
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
                if(!$this->allowRequest($request)){
                    $response = new Response('', Response::HTTP_UNAUTHORIZED);
                    $response->headers->set('Vary', 'sec-fetch-site');
                    $event->setResponse($response);
                }
            }
    }
    /**
     * Method to check the current request against base Fetch Metadata security guidelines.
     * Guide based on https://web.dev/fetch-metadata/ article
     *
     * @param Request $req 
     * @return void
     */
    private function allowRequest(Request $req) 
    {
        $headers = $req->headers;
        if(!$headers->get('sec-fetch-site')){
            return true;
        }
        
        if(in_array($headers->get('sec-fetch-site'), array('same-origin', 'same-site', 'none'))){
            return true;
        }

        if($headers->get('sec-fetch-mode') == 'navigate' and $req->getMethod() == 'GET'
            and !in_array($headers->get('sec-fetch-dest'), array('object', 'embed'))) {
            return true;
        }

        if(in_array($req->getUri(), $this->corsEndpoints)) {
            return true;
        }
        return false;
    }
}