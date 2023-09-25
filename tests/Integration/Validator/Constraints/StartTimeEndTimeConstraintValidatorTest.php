<?php

namespace App\Tests\Integration\Validator\Constraints;

use App\Model\BatchTutoringSessionCreationModel;
use App\Tests\Fixtures\TutoringFixturesProvider;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StartTimeEndTimeConstraintValidatorTest extends KernelTestCase
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

    public function testCreateValidBatchTutoringSessionCreationStartDateEndDate(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $batchTutoringSessionCreation = (new BatchTutoringSessionCreationModel())
            ->setTutoring($tutoring)
            ->setStartTime(new DateTime('2022-02-16 14:00'))
            ->setEndTime(new DateTime('2022-02-16 16:00'))
            ->setStartDate(new DateTime('2022-02-13'))
            ->setEndDate(new DateTime('2022-02-20'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
            ->setWeekDays(['monday', 'tuesday'])
        ;

        $errors = $this->validator->validate($batchTutoringSessionCreation);
        $this->assertCount(0, $errors);
    }

    public function testCreateInvalidBatchTutoringSessionCreationStartDateAfterEndDate(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $batchTutoringSessionCreation = (new BatchTutoringSessionCreationModel())
            ->setTutoring($tutoring)
            ->setStartTime(new DateTime('2022-02-16 14:00'))
            ->setEndTime(new DateTime('2022-02-16 16:00'))
            ->setStartDate(new DateTime('2022-02-15'))
            ->setEndDate(new DateTime('2022-02-13'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
            ->setWeekDays(['monday', 'tuesday'])
        ;

        $errors = $this->validator->validate($batchTutoringSessionCreation);

        $this->assertCount(2, $errors);
        $this->assertEquals('La date de début ne peut pas être postérieure à la date de fin', $errors[0]->getMessage());
    }
}
