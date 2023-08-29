<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class CasLogoutListener
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly string $logoutUrl)
    {
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(LogoutEvent $logoutEvent): void
    {
        $this->tokenStorage->setToken(null);

        $logoutEvent->getRequest()->getSession()->invalidate();
        $logoutEvent->setResponse(new RedirectResponse($this->logoutUrl));
    }
}
