<?php

namespace App\Service;

use App\Entity\Student;
use App\Entity\TutoringSession;
use App\Exception\BatchTutoringSessionCreationException;
use App\Model\BatchTutoringSessionCreationModel;
use App\Utils\DateUtils;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class BatchTutoringSessionCreationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private LoggerInterface $logger,
    ) {
    }

    public function batchCreateSessions(BatchTutoringSessionCreationModel $batchTutoringSessionCreationModel): void
    {
        /** @var Student $user */
        $user = $this->security->getUser();

        $selectedWeekdays = $batchTutoringSessionCreationModel->getWeekDays();
        /** @var DateTimeInterface[] $dates */
        $dates = DateUtils::getAllDatesBetweenDatesByWeekdays($batchTutoringSessionCreationModel->getStartDate(), $batchTutoringSessionCreationModel->getEndDate(), $selectedWeekdays);
        $this->entityManager->getConnection()->beginTransaction();
        try {
            foreach ($dates as $date) {
                $startDateTime = clone $date->setTime(hour: $batchTutoringSessionCreationModel->getStartTime()->format('H'), minute: $batchTutoringSessionCreationModel->getStartTime()->format('i'));
                $endDateTime = clone $date->setTime(hour: $batchTutoringSessionCreationModel->getEndTime()->format('H'), minute: $batchTutoringSessionCreationModel->getEndTime()->format('i'));

                $tutoringSession = (new TutoringSession())
                    ->setBuilding($batchTutoringSessionCreationModel->getBuilding())
                    ->setRoom($batchTutoringSessionCreationModel->getRoom())
                    ->setCreatedBy($user)
                    ->setTutoring($batchTutoringSessionCreationModel->getTutoring())
                    ->setStartDateTime($startDateTime)
                    ->setEndDateTime($endDateTime)
                    ->addTutor($user)
                ;
                $this->entityManager->persist($tutoringSession);
            }
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->logger->error($e);
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->getConnection()->rollBack();
            }

            throw new BatchTutoringSessionCreationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
