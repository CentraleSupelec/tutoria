<?php

namespace App\Service;

use App\Entity\Student;
use App\Exception\ExternalConnector\ExternalConnectorException;
use App\Repository\StudentRepository;
use App\Service\ExternalConnector\UserExternalProviderInterface;
use App\Utils\PasswordUpdater;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Contracts\Service\Attribute\Required;

class StudentManager extends AbstractUserManager
{
    private ?UserExternalProviderInterface $userExternalProvider = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordUpdater $passwordUpdater,
        private readonly StudentRepository $studentRepository,
    ) {
        parent::__construct($entityManager, $passwordUpdater);
    }

    #[Required]
    public function setUserExternalProvider(?UserExternalProviderInterface $userExternalProvider): void
    {
        $this->userExternalProvider = $userExternalProvider;
    }

    public function findUserByEmail(?string $email): ?Student
    {
        return $this->studentRepository->findOneBy(['email' => $email]);
    }

    public function findUserByExternalSourceId(string $externalSourceId): ?Student
    {
        return $this->studentRepository->findOneBy(['externalSourceId' => $externalSourceId]);
    }

    public function findUserById($id): ?Student
    {
        return $this->studentRepository->findOneBy(['id' => $id]);
    }

    /**
     * @throws ExternalConnectorException
     */
    public function createOrUpdateStudentFromExternalSourceByEmail(string $email): ?Student
    {
        return $this->userExternalProvider?->createOrUpdateUserFromExternalSourceByEmail($email);
    }

    /**
     * @throws ExternalConnectorException|LogicException
     */
    public function updateExistingStudentFromExternalSourceByEmail(Student $student, string $csEmail): ?Student
    {
        if ($csEmail !== $student->getEmail()) {
            throw new LogicException(sprintf('Student with id %s does not have %s as institutional email; it has %s.', $student->getId(), $csEmail, $student->getEmail()));
        }

        return $this->userExternalProvider?->createOrUpdateUserFromExternalSourceByEmail($csEmail);
    }

    /**
     * @throws ExternalConnectorException
     */
    public function createOrUpdateStudentFromExternalSourceByExternalSourceId(
        string $externalSourceId, array $externalSourceUser = null
    ): ?Student {
        return $this->userExternalProvider?->createOrUpdateUserFromExternalSourceByExternalSourceId(
            $externalSourceId, $externalSourceUser
        );
    }

    public function refreshStudentFromExternalSource(Student $student): Student
    {
        try {
            return $this->userExternalProvider?->refreshUserFromExternalSource($student) ?? $student;
        } catch (ExternalConnectorException|UnexpectedTypeException) {
            return $student;
        }
    }

    public function signGdprConsent(Student $student): void
    {
        $student->setConsentSignedAt(new DateTime());
        $this->updateUser($student);
    }
}
