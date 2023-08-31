<?php

namespace App\Tests\Fixtures;

use App\Entity\AcademicLevel;
use App\Entity\Building;
use App\Entity\Campus;
use App\Entity\Student;
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

    public static function getCampus(?EntityManagerInterface $entityManager): Campus
    {
        $campus = (new Campus())
            ->setName('Gif-sur-Yvette');

        if (null !== $entityManager) {
            $entityManager->persist($campus);
            $entityManager->flush();
        }

        return $campus;
    }

    public static function getBuilding(?EntityManagerInterface $entityManager): Building
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

    public static function getAcademicLevel(?EntityManagerInterface $entityManager): AcademicLevel
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

    public static function getTutoring(?EntityManagerInterface $entityManager): Tutoring
    {
        $tutor = StudentFixturesProvider::getTutor($entityManager);
        $building = self::getBuilding($entityManager);
        $academicLevel = self::getAcademicLevel($entityManager);

        $tutoring = (new Tutoring())
            ->setAcademicLevel($academicLevel)
            ->setDefaultBuilding($building)
            ->setDefaultRoom('E110')
            ->addTutor($tutor)
            ->setName(sprintf('%s@%s', $academicLevel->getNameFr(), $building->getCampus()->getName()))
        ;

        if (null !== $entityManager) {
            $entityManager->persist($tutoring);
            $entityManager->flush();
        }

        return $tutoring;
    }

    public static function getTutoringSession(Tutoring $tutoring, ?EntityManagerInterface $entityManager): TutoringSession
    {
        $tutoringSession = (new TutoringSession())
            ->setCreatedBy($tutoring->getTutors()[0])
            ->setStartDateTime((new DateTime())->add(new DateInterval('P1D')))
            ->setEndDateTime((new DateTime())->add(new DateInterval('P1DT1H')))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom('E110')
            ->addTutor($tutoring->getTutors()[0])
            ->setTutoring($tutoring);

        if (null !== $entityManager) {
            $entityManager->persist($tutoringSession);
            $entityManager->flush();
        }

        return $tutoringSession;
    }

    public static function getTutoringSessions(Student $tutee, ?EntityManagerInterface $entityManager): array
    {
        $tutoring = self::getTutoring($entityManager);
        $firstTutoringSession = self::getTutoringSession($tutoring, $entityManager);
        $firstTutoringSession->addStudent($tutee);

        $secondTutoringSession = (new TutoringSession())
            ->setCreatedBy($tutoring->getTutors()[0])
            ->setStartDateTime(DateTime::createFromFormat('Y-m-d', '2020-01-01'))
            ->setEndDateTime(DateTime::createFromFormat('Y-m-d', '2020-01-01')->add(new DateInterval('PT1H')))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom('A105')
            ->addTutor($tutoring->getTutors()[0])
            ->addStudent($tutee)
            ->setTutoring($tutoring);

        $thirdTutoringSession = (new TutoringSession())
            ->setCreatedBy($tutoring->getTutors()[0])
            ->setStartDateTime(DateTime::createFromFormat('Y-m-d', '2020-01-01'))
            ->setEndDateTime(DateTime::createFromFormat('Y-m-d', '2020-01-01')->add(new DateInterval('PT1H')))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom('A102')
            ->addTutor($tutoring->getTutors()[0])
            ->addStudent($tutee)
            ->setTutoring($tutoring);

        if (null !== $entityManager) {
            $entityManager->persist($secondTutoringSession);
            $entityManager->persist($thirdTutoringSession);
            $entityManager->flush();
        }

        return [$firstTutoringSession, $secondTutoringSession, $thirdTutoringSession];
    }

    public static function getTutorings(?EntityManagerInterface $entityManager): array
    {
        $firstTutoring = self::getTutoring($entityManager);

        $secondCampus = (new Campus())
            ->setName('Metz');

        $secondBuilding = (new Building())
            ->setCampus($secondCampus)
            ->setName('A');

        $secondTutoring = (new Tutoring())
            ->setAcademicLevel($firstTutoring->getAcademicLevel())
            ->setDefaultBuilding($secondBuilding)
            ->setDefaultRoom('D210')
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
