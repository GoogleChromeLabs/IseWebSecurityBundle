<?php

namespace Ise\WebSecurityBundle\EventSubscriber;

use Ise\WebSecurityBundle\Options\ConfigProviderInterface;
use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FetchMetadataRequestSubscriber implements EventSubscriberInterface
{
    private $fetchMetadataPolicyProvider;
    private $configProvider;
    private $logger;
    private $requestBlockedMessage = '%1$s request from host %2$s blocked by Fetch Metadata Policy: %3$s';

    public function __construct(FetchMetadataPolicyProvider $fetchMetadataPolicyProvider, ConfigProviderInterface $configProvider, LoggerInterface $securityLogger)
    {
        $this->fetchMetadataPolicyProvider = $fetchMetadataPolicyProvider;
        $this->configProvider = $configProvider;
        $this->logger = $securityLogger;
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
            $response = new Response();
            $response->headers->set('Vary', 'sec-fetch-site, sec-fetch-dest, sec-fetch-mode');

            if (!$fetchMetadataPolicy->applyPolicy($request)) {
                $this->logRejection($request, $options['fetch_metadata']['policy']);

                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
                $event->setResponse($response);
            }
        }
    }
    /**
     * Log rejection function. Logs if a request has been rejected by the fetch metadata policy.
     * Records the Url, and which policy rejected the request.
     *
     * @param String $policy, A reference string to the Fetch Metadata policy used in the request. If Null assumed Default Policy
     * @param Request $request, The request
     * @return void
     */
    private function logRejection(Request $request, $policy): void
    {
        $debugContext = [
            'request' => $request->getContent(),
            'policy' => $policy
        ];
        $fetchMetaPolicy = $policy ?? 'Default Policy';
        $rejectMessage = sprintf($this->requestBlockedMessage, $request->getMethod(), $request->getUri(), $fetchMetaPolicy);
        $this->logger->debug($rejectMessage, $debugContext);
    }
}
