<?php

namespace Ise\WebSecurityBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends AbstractController
{
    private $logger;

    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Report middleware for handling CSP requests.
     *
     * @param Request $req
     * @return void
     */
    public function report(Request $req)
    {
        $this->logger->debug($req->getContent());
        $res = new JsonResponse($req->getContent());
        return $res;
    }
}
