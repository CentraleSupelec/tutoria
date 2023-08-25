<?php

namespace App\Tests\Fixtures;

use App\Entity\AcademicLevel;
use App\Entity\Building;
use App\Entity\Campus;
use App\Entity\Tutoring;
use App\Entity\TutoringSession;
use DateInterval;
use DateTime;
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

    public static function getTutoringSession(Tutoring $tutoring, EntityManagerInterface $entityManager): TutoringSession
    {
        $tutoringSession = (new TutoringSession())
            ->setCreatedBy($tutoring->getTutors()[0])
            ->setStartDateTime(new DateTime())
            ->setEndDateTime((new DateTime())->add(new DateInterval('PT1H')))
            ->setBuilding($tutoring->getBuilding())
            ->setRoom('E110')
            ->addTutor($tutoring->getTutors()[0])
            ->setTutoring($tutoring);

        if (null !== $entityManager) {
            $entityManager->persist($tutoringSession);
            $entityManager->flush();
        }

        return $tutoringSession;
    }

    public static function getTutorings(EntityManagerInterface $entityManager): array
    {
        $firstTutoring = self::getTutoring($entityManager);

        $secondCampus = (new Campus())
            ->setName('Metz');

        $secondBuilding = (new Building())
            ->setCampus($secondCampus)
            ->setName('A');

        $secondTutoring = (new Tutoring())
            ->setAcademicLevel($firstTutoring->getAcademicLevel())
            ->setBuilding($secondBuilding)
            ->setRoom('D210')
            ->addTutor($firstTutoring->getTutors()[0])
            ->setName(sprintf('%s@%s', $firstTutoring->getAcademicLevel()->getNameFr(), $secondCampus->getName()));

        if (null !== $entityManager) {
            $entityManager->persist($secondCampus);
            $entityManager->persist($secondBuilding);
            $entityManager->persist($secondTutoring);
            $entityManager->flush();
        }

        return [$firstTutoring, $secondTutoring];
    }
}
