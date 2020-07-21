<?php

namespace Ise\WebSecurityBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ReportCollector extends DataCollector
{
    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        if ($request->headers->get('X-REPORT')) {
            $this->data = [
                'csp-report' => $request->getContent()
            ];
        }
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getName()
    {
        return 'ise.web_security.report_collector';
    }

    public function getContent()
    {
        return $this->data['csp-report'];
    }
}
