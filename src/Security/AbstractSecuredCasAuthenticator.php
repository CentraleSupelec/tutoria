<?php

namespace App\Security;

use EcPhp\CasBundle\Security\Core\User\CasUserProviderInterface;
use EcPhp\CasLib\CasInterface;
use EcPhp\CasLib\Introspection\Contract\ServiceValidate;
use EcPhp\CasLib\Utils\Uri;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AbstractSecuredCasAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        protected HttpMessageFactoryInterface $httpMessageFactory,
        protected CasInterface $cas,
        protected HttpFoundationFactoryInterface $httpFoundationFactory,
        protected CasUserProviderInterface $casUserProvider,
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $authenticationException): ?Response
    {
        $uri = $this->toPsr($request)->getUri();

        if (!Uri::hasParams($uri, 'ticket')) {
            return null;
        }

        // Remove the ticket parameter.
        $uri = Uri::removeParams(
            $uri,
            'ticket'
        );

        return new RedirectResponse((string) Uri::withParam($uri, 'renew', 'true'));
    }

    public function start(Request $request, AuthenticationException $authenticationException = null): RedirectResponse|JsonResponse|Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(
                ['message' => 'Authentication required'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $response = $this->cas->login($request->query->all());

        return $response instanceof ResponseInterface ?
            $this->httpFoundationFactory->createResponse($response) :
            new RedirectResponse('/');
    }

    public function supports(Request $request): ?bool
    {
        return $this
            ->cas
            ->withServerRequest($this->toPsr($request))
            ->supportAuthentication();
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $response = $this
            ->cas
            ->withServerRequest($this->toPsr($request))
            ->requestTicketValidation();

        if (!$response instanceof ResponseInterface) {
            throw new AuthenticationException('Unable to authenticate the user with such service ticket.');
        }

        try {
            $introspection = $this->cas->detect($response);
        } catch (InvalidArgumentException $exception) {
            throw new AuthenticationException($exception->getMessage(), 0, $exception);
        }

        if (!$introspection instanceof ServiceValidate) {
            throw new AuthenticationException('Failure in the returned response');
        }

        $casUser = $this->casUserProvider->loadUserByResponse($response);

        return new SelfValidatingPassport(
            new UserBadge(
                $casUser->getUserIdentifier(),
                static fn (): UserInterface => $casUser
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(
            (string) Uri::removeParams(
                $this->toPsr($request)->getUri(),
                'ticket',
                'renew'
            )
        );
    }

    protected function toPsr(Request $request): ServerRequestInterface
    {
        // As we cannot decorate the Symfony Request object, we convert it into
        // a PSR Request so we can override the PSR HTTP Message factory if
        // needed.
        // See the reasons at https://github.com/ecphp/cas-lib/issues/5
        return $this->httpMessageFactory->createRequest($request);
    }
}
