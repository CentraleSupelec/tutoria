<?php

namespace App\Tests\Integration\Validator\Constraints;

use App\Entity\TutoringSession;
use App\Tests\Fixtures\TutoringFixturesProvider;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OnlineMeetingUriConstraintValidatorTest extends KernelTestCase
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

    public function testCreateValidTutoringSessionNotRemote(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $tutoringSession = (new TutoringSession())
            ->setTutoring($tutoring)
            ->setStartDateTime(new DateTime('2022-02-16 14:00'))
            ->setEndDateTime(new DateTime('2022-02-16 15:00'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
            ->setIsRemote(false)
        ;

        $errors = $this->validator->validate($tutoringSession);

        $this->assertCount(0, $errors);
    }

    public function testCreateValidTutoringSessionRemote(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $tutoringSession = (new TutoringSession())
            ->setTutoring($tutoring)
            ->setStartDateTime(new DateTime('2022-02-16 14:00'))
            ->setEndDateTime(new DateTime('2022-02-16 15:00'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
            ->setIsRemote(true)
            ->setOnlineMeetingUri('https://www.google.com')
        ;

        $errors = $this->validator->validate($tutoringSession);

        $this->assertCount(0, $errors);
    }

    public function testCreateInvalidTutoringSessionRemote(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $tutoringSession = (new TutoringSession())
            ->setTutoring($tutoring)
            ->setStartDateTime(new DateTime('2022-02-16 14:00'))
            ->setEndDateTime(new DateTime('2022-02-16 15:00'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
            ->setIsRemote(true)
        ;

        $errors = $this->validator->validate($tutoringSession);

        $this->assertCount(1, $errors);
        $this->assertEquals("Merci d'entrer un lien lorsque l'option \"Distanciel\" est sélectionnée", $errors[0]->getMessage());
    }
}
