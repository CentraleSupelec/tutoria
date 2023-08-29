<?php

namespace App\Tests\Fixtures;

use App\Entity\AcademicLevel;
use App\Entity\Building;
use App\Entity\Campus;
use App\Entity\Tutoring;
use Doctrine\ORM\EntityManagerInterface;

class TutoringFixturesProvider
{
    public function __construct(
    ) {
    }

    public static function getCampus(EntityManagerInterface $entityManager): Campus
    {
        $campus = (new Campus())
            ->setName('Gif-sur-Yvette');

        if (null !== $entityManager) {
            $entityManager->persist($campus);
            $entityManager->flush();
        }

        return $campus;
    }

    public static function getBuilding(EntityManagerInterface $entityManager): Building
    {
        $campus = self::getCampus($entityManager);

        $building = (new Building())
            ->setCampus($campus)
            ->setName('Eiffel');

        if (null !== $entityManager) {
            $entityManager->persist($building);
            $entityManager->flush();
        }

        return $building;
    }

    public static function getAcademicLevel(EntityManagerInterface $entityManager): AcademicLevel
    {
        $academicLevel = (new AcademicLevel())
            ->setNameFr('M1 en Mathématique')
            ->setNameEn('M1 in Mathematics')
            ->setAcademicYear('2023-2024');

        if (null !== $entityManager) {
            $entityManager->persist($academicLevel);
            $entityManager->flush();
        }

        return $academicLevel;
    }

    public static function getTutoring(EntityManagerInterface $entityManager): Tutoring
    {
        $tutor = StudentFixturesProvider::getTutor($entityManager);
        $building = self::getBuilding($entityManager);
        $academicLevel = self::getAcademicLevel($entityManager);

        $tutoring = (new Tutoring())
            ->setAcademicLevel($academicLevel)
            ->setBuilding($building)
            ->setRoom('E110')
            ->addTutor($tutor)
            ->setName(sprintf('%s@%s', $academicLevel->getNameFr(), $building->getCampus()->getName()));

        if (null !== $entityManager) {
            $entityManager->persist($tutoring);
            $entityManager->flush();
        }

        return $tutoring;
    }
}
