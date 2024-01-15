<?php

// src/EventListener/DomainAccessListener.php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class DomainAccessListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $host = $request->getHost();

        // Define the allowed domains based on the environment
        $allowedDomain = $this->getAllowedDomain($request);

        // Check if the request is for the admin panel or API and if the domain is allowed
        if (($this->isAdminPath($request) || $this->isApiPath($request)) && $host !== $allowedDomain) {
            $response = new RedirectResponse('/');
            $event->setResponse($response);
        }
    }

    private function isApiPath($request)
    {
        // Check if the request path is within the API paths
        return strpos($request->getPathInfo(), '/merchant/v1/invoices') === 0;
    }

    private function isAdminPath($request)
    {
        // Check if the request path is for the admin panel
        return strpos($request->getPathInfo(), '/admin_login') === 0;
    }

    private function getAllowedDomain($request)
    {
        // Check the environment and get the appropriate ALLOWED_DOMAIN value
        if ($_ENV['APP_ENV'] === 'prod') {
            if ($this->isApiPath($request)) {
                // Define the allowed domain for the API in production
                return $_ENV['ALLOWED_DOMAIN_API_PROD'];
            } else {
                // Define the allowed domain for the admin panel in production
                return $_ENV['ALLOWED_DOMAIN_PROD'];
            }
        } else {
            if ($this->isApiPath($request)) {
                // Define the allowed domain for the API in development
                return $_ENV['ALLOWED_DOMAIN_API_DEV'];
            } else {
                // Define the allowed domain for the admin panel in development
                return $_ENV['ALLOWED_DOMAIN_DEV'];
            }
        }
    }
}
