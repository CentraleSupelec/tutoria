<?php

namespace App\Security\Student;

use App\Entity\Student;
use App\Repository\StudentRepository;
use EcPhp\CasBundle\Security\Core\User\CasUserInterface;
use EcPhp\CasBundle\Security\Core\User\CasUserProviderInterface;
use EcPhp\CasLib\Introspection\Contract\IntrospectorInterface;
use EcPhp\CasLib\Introspection\Contract\ServiceValidate;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentProvider implements CasUserProviderInterface
{
    public function __construct(
        private readonly StudentRepository $studentRepository,
        private readonly IntrospectorInterface $introspector,
    ) {
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $student = $this->studentRepository->findOneBy(['email' => $identifier]);

        if (!$student instanceof Student) {
            throw new UserNotFoundException(sprintf('Username "%s" does not exist.', $identifier));
        }

        return $student;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$this->supportsClass($user::class)) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', Student::class, $user::class));
        }

        /** @var Student $user */
        if (!($student = $this->studentRepository->findOneBy(['id' => $user->getId()])) instanceof Student) {
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
            // The 'eduPersonPrincipalName' attribute is specific for UPS CAS
            $email = $introspection->getCredentials()['attributes']['eduPersonPrincipalName'][0] ?? $introspection->getCredentials()['user'] ?? null;
            $student = $this->studentRepository->findOneBy(['email' => $email]);
            if ($student instanceof Student) {
                return $student;
            }

            throw new AuthenticationException('User not found');
        }

        throw new AuthenticationException('Unable to load user from response.');
    }
}
