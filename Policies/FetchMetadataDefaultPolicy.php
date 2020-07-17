<?php

namespace Ise\WebSecurityBundle\Policies;
use Ise\WebSecurityBundle\Policies\FetchMetadataPolicyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class FetchMetadataDefaultPolicy implements FetchMetadataPolicyInterface
{
    /**
     * CorsEndpoints denotes the allowed cross origin endpoints as part of the Default Fetch metadata policy
     *
     * @var array
     */
    private $corsEndpoints;

    public function __construct($corsEndpoints = [])
    {
        $this->corsEndpoints = $corsEndpoints;
    }
    /**
     * apllyPolicy Applies the default Fetch Metadata Policy as defined by https://web.dev/fetch-metadata/
     *
     * @param Request $req
     * @return boolean
     */
    public function applyPolicy(Request $req): bool
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

        if(in_array($req->getPathInfo(), $this->corsEndpoints)) {
            return true;
        }
        return false;
    }
}