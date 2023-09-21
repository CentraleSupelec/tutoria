<?php

namespace App\Tests\Integration\Validator\Constraints;

use App\Entity\TutoringSession;
use App\Tests\Fixtures\TutoringFixturesProvider;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StartDateTimeEndDateTimeConstraintValidatorTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel([
            'debug' => false,
            'environment' => 'test',
        ]);

        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    public function testCreateValidTutoringSessionStartDateTimeEndDateTime(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $tutoringSession = (new TutoringSession())
            ->setTutoring($tutoring)
            ->setStartDateTime(new DateTime('2022-02-16 14:00'))
            ->setEndDateTime(new DateTime('2022-02-16 16:00'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
        ;
        $errors = $this->validator->validate($tutoringSession);

        $this->assertCount(0, $errors);
    }

    public function testCreateInvalidTutoringSessionStartDateDifferentThanEndDate(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $tutoringSession = (new TutoringSession())
            ->setTutoring($tutoring)
            ->setStartDateTime(new DateTime('2022-02-15 14:00'))
            ->setEndDateTime(new DateTime('2022-02-16 11:00'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
        ;

        $errors = $this->validator->validate($tutoringSession);

        $this->assertCount(1, $errors);
        $this->assertEquals('La date de début est différente de la date de fin', $errors[0]->getMessage());
    }

    public function testCreateInvalidTutoringSessionStartTimeAfterEndTime(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $tutoringSession = (new TutoringSession())
            ->setTutoring($tutoring)
            ->setStartDateTime(new DateTime('2022-02-16 14:00'))
            ->setEndDateTime(new DateTime('2022-02-16 11:00'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
        ;

        $errors = $this->validator->validate($tutoringSession);

        $this->assertCount(1, $errors);
        $this->assertEquals("L'horaire de début est après l'horaire de fin", $errors[0]->getMessage());
    }
}
