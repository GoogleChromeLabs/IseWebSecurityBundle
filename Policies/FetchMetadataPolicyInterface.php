<?php

namespace Ise\WebSecurityBundle\Policies;

use Symfony\Component\HttpFoundation\Request;

interface FetchMetadataPolicyInterface
{
    /**
     * Apply Policy interface. This Function should process a Symfony HTTP Kernel request, apply Fetch a Fetch Metadata policy
     * and return TRUE if the request passes the policy and should be accepted, or FALSE if it does not pass the policy.
     *
     * @param Request $request A symfony HTTP kernel request
     * @return boolean
     */
    public function applyPolicy(Request $request): bool;
}
