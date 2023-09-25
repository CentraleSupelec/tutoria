<?php

namespace App\Tests\Integration\Validator\Constraints;

use App\Model\BatchTutoringSessionCreationModel;
use App\Tests\Fixtures\TutoringFixturesProvider;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AllDefaultWeekdaysHaveAtLeastOneSessionConstraintValidatorTest extends KernelTestCase
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

    public function testCreateValidBatchTutoringSessionCreationAllDefaultWeekdaysHaveAtLeastOneSession(): void
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

    public function testCreateInvalidBatchTutoringSessionCreationAllDefaultWeekdaysHaveAtLeastOneSession(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $batchTutoringSessionCreation = (new BatchTutoringSessionCreationModel())
            ->setTutoring($tutoring)
            ->setStartTime(new DateTime('2022-02-16 14:00'))
            ->setEndTime(new DateTime('2022-02-16 16:00'))
            ->setStartDate(new DateTime('2022-02-13'))
            ->setEndDate(new DateTime('2022-02-15'))
            ->setBuilding($tutoring->getDefaultBuilding())
            ->setRoom($tutoring->getDefaultRoom())
            ->setWeekDays(['monday', 'tuesday', 'friday'])
        ;

        $errors = $this->validator->validate($batchTutoringSessionCreation);

        $this->assertCount(1, $errors);
        $this->assertEquals('Les dates entrées ne sont pas compatibles avec les jours de la semaine indiqués', $errors[0]->getMessage());
    }
}
