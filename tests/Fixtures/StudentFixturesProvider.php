<?php

namespace App\Tests\Fixtures;

use App\Entity\Student;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class StudentFixturesProvider
{
    public function __construct(
    ) {
    }

    public static function getTutor(?EntityManagerInterface $entityManager): Student
    {
        $tutor = (new Student())
            ->setFirstName('Michael')
            ->setLastName('Jackson')
            ->setEmail('michael.jackson@upsaclay.fr')
            ->setRoles([Student::ROLE_TUTOR])
            ->setCreatedAt(new DateTime('2022-01-15 10:22:35'))
            ->setUpdatedAt(new DateTime('2022-01-15 10:22:35'));

        if (null !== $entityManager) {
            $entityManager->persist($tutor);
            $entityManager->flush();
        }

        return $tutor;
    }

    public static function getStudent(?EntityManagerInterface $entityManager): Student
    {
        $tutor = (new Student())
            ->setFirstName('Steve')
            ->setLastName('Jobs')
            ->setEmail('steve.jobs@upsaclay.fr')
            ->setRoles([Student::ROLE_TUTORED])
            ->setCreatedAt(new DateTime('2022-01-15 10:22:35'))
            ->setUpdatedAt(new DateTime('2022-01-15 10:22:35'));

        if (null !== $entityManager) {
            $entityManager->persist($tutor);
            $entityManager->flush();
        }

        return $tutor;
    }
}
