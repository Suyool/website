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
        $allowedDomain = $this->getAllowedDomain();

        if ($this->isRestrictedPath($request) && $host !== $allowedDomain) {
            $response = new RedirectResponse('/');
            $event->setResponse($response);
        }
    }

    private function isRestrictedPath($request)
    {
        return strpos($request->getPathInfo(), '/admin_login') === 0;
    }

    private function getAllowedDomain()
    {
        // Check the environment and get the appropriate ALLOWED_DOMAIN value
        if ($_ENV['APP_ENV'] === 'prod') {
            return $_ENV['ALLOWED_DOMAIN_PROD'];
        } else {
            return $_ENV['ALLOWED_DOMAIN_DEV'] ;
        }
    }
}
