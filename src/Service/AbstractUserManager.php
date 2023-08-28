<?php

namespace App\Service;

use App\Entity\Student;
use App\Model\UserPasswordInterface;
use App\Utils\PasswordUpdater;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractUserManager
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected PasswordUpdater $passwordUpdater,
    ) {
    }

    public function updatePassword(UserPasswordInterface $userPassword): void
    {
        $this->passwordUpdater->hashPassword($userPassword);
    }

    public function updateUser(Student $student): void
    {
        if ($student instanceof UserPasswordInterface) {
            $this->updatePassword($student);
        }

        $this->entityManager->persist($student);
        $this->entityManager->flush();
    }

    abstract public function findUserByEmail(?string $email): ?Student;

    abstract public function findUserById($id): ?Student;
}
