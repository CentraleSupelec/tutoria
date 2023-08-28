<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestLocaleListener
{
    public function __construct(
        private readonly string $localeCookieName,
        private readonly string $localeQueryName,
        private readonly array $allowedLocales,
    ) {
    }

    public function onKernelRequest(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        $localeFromQuery = $request->query->get($this->localeQueryName);
        $localeFromCookies = $request->cookies->get($this->localeCookieName);
        $locale = $localeFromQuery ?? $localeFromCookies ?? 'fr';

        if (!in_array($locale, $this->allowedLocales)) {
            $locale = 'fr';
        }

        $request->setLocale($locale);
    }
}
