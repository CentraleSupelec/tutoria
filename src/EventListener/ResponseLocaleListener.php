<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseLocaleListener
{
    public function __construct(private readonly string $localeCookieName)
    {
    }

    public function onKernelResponse(ResponseEvent $responseEvent): void
    {
        $request = $responseEvent->getRequest();
        $response = $responseEvent->getResponse();

        $localeFromCookies = $request->cookies->get($this->localeCookieName);
        $locale = $request->getLocale();

        // Store locale in users cookies
        if ($locale !== $localeFromCookies) {
            $response->headers->setCookie(new Cookie($this->localeCookieName, $locale));
        }
    }
}
