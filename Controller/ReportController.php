<?php

namespace Ise\WebSecurityBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends AbstractController
{
    private $logger;
    private static $storageTest = [];
    
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
        $this->logger->info($req->getContent());
        array_push(ReportController::$storageTest, $req->getContent());
        $res = new JsonResponse($req->getContent());
        return $res;
    }
    public function index(Request $req)
    {
        return new JsonResponse(ReportController::$storageTest);
    }
}
