<?php

namespace App\Security\Student;

use App\Entity\Student;
use App\Exception\ExternalConnector\ExternalConnectorException;
use App\Service\StudentManager;
use EcPhp\CasBundle\Security\Core\User\CasUserInterface;
use EcPhp\CasBundle\Security\Core\User\CasUserProviderInterface;
use EcPhp\CasLib\Introspection\Contract\IntrospectorInterface;
use EcPhp\CasLib\Introspection\Contract\ServiceValidate;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentProvider implements CasUserProviderInterface
{
    public function __construct(
        private readonly StudentManager $studentManager,
        private readonly LoggerInterface $logger,
        private readonly IntrospectorInterface $introspector,
    ) {
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->studentManager->findUserByEmail($identifier);

        if ($user && $user->getEmail()) {
            try {
                $this->studentManager->updateExistingStudentFromExternalSourceByEmail($user, $user->getEmail());
            } catch (ExternalConnectorException $exception) {
                $this->logger->warning(sprintf('Not refreshing %s from Geode: %s', $identifier, $exception));
            }
        }

        if (!$user instanceof Student) {
            try {
                $user = $this->studentManager->createOrUpdateStudentFromExternalSourceByEmail($identifier);
            } catch (ExternalConnectorException $exception) {
                $this->logger->warning($exception);
                throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier), 0, $exception);
            }
        }

        if (!$user instanceof Student) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', Student::class, $user::class));
        }

        /** @var Student $user */
        if (!($student = $this->studentManager->findUserById($user->getId())) instanceof Student) {
            throw new UserNotFoundException(sprintf('User with ID "%s" could not be reloaded.', $user->getId()));
        }

        return $student;
    }

    public function supportsClass($class): bool
    {
        return Student::class === $class;
    }

    public function loadUserByResponse(ResponseInterface $response): CasUserInterface
    {
        try {
            $introspection = $this->introspector->detect($response);
        } catch (InvalidArgumentException $exception) {
            throw new AuthenticationException($exception->getMessage());
        }

        if ($introspection instanceof ServiceValidate) {
            $student = $this->studentManager->findUserByEmail($introspection->getCredentials()['user']);
            if ($student instanceof Student) {
                return $student;
            }

            return (new Student($introspection->getCredentials()))
                ->setEmail($introspection->getCredentials()['user'] ?? null);
        }

        throw new AuthenticationException('Unable to load user from response.');
    }
}
