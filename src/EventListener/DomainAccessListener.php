<?php


// src/EventListener/DomainAccessListener.php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class DomainAccessListener
{
    private $allowedDomain = 'bk.suyool';

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $host = $request->getHost();

        if ($this->isRestrictedPath($request) && $host !== $this->allowedDomain) {
            $response = new RedirectResponse('/'); // Redirect to homepage or a different route
            $event->setResponse($response);
        }
    }

    private function isRestrictedPath($request)
    {
        // Define your logic to check if the request should be restricted
        // For example, check if the request path matches the admin route
        return strpos($request->getPathInfo(), '/admin_login') === 0;
    }
}
