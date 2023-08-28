<?php

namespace App\Security\Student;

use App\Security\AbstractSecuredCasAuthenticator;
use EcPhp\CasLib\CasInterface;
use EcPhp\CasLib\Utils\Uri;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class StudentSecuredCasAuthenticator extends AbstractSecuredCasAuthenticator
{
    public function __construct(
        HttpMessageFactoryInterface $httpMessageFactory,
        CasInterface $cas,
        HttpFoundationFactoryInterface $httpFoundationFactory,
        StudentProvider $studentProvider,
        private readonly LoggerInterface $logger,
        private readonly Environment $twigEnvironment,
    ) {
        parent::__construct($httpMessageFactory, $cas, $httpFoundationFactory, $studentProvider);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
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

        try {
            return new Response($this->twigEnvironment->render(
                'security/student-not-found.html.twig',
                [
                    'message' => $authenticationException->getMessage(),
                    'retryURL' => (string) Uri::withParam($uri, 'renew', 'true'),
                ]
            ));
        } catch (LoaderError|RuntimeError|SyntaxError $authenticationException) {
            $this->logger->error($authenticationException);

            throw $authenticationException;
        }
    }
}
