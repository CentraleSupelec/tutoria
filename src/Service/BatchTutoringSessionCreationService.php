<?php

namespace App\Service;

use App\Entity\TutoringSession;
use App\Exception\BatchTutoringSessionCreationException;
use App\Model\BatchTutoringSessionCreationModel;
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
        $user = $this->security->getUser();

        $selectedWeekdays = [];
        if ($batchTutoringSessionCreationModel->getMondaySelected()) {
            $selectedWeekdays[] = 1;
        }
        if ($batchTutoringSessionCreationModel->getTuesdaySelected()) {
            $selectedWeekdays[] = 2;
        }
        if ($batchTutoringSessionCreationModel->getWednesdaySelected()) {
            $selectedWeekdays[] = 3;
        }
        if ($batchTutoringSessionCreationModel->getThursdaySelected()) {
            $selectedWeekdays[] = 4;
        }
        if ($batchTutoringSessionCreationModel->getFridaySelected()) {
            $selectedWeekdays[] = 5;
        }
        /** @var DateTimeInterface[] $dates */
        $dates = $this->getAllDatesBetweenDatesByWeekdays($batchTutoringSessionCreationModel->getStartDate(), $batchTutoringSessionCreationModel->getEndDate(), $selectedWeekdays);
        $this->entityManager->getConnection()->beginTransaction();
        try {
            foreach ($dates as $date) {
                $startDateTime = clone $date->setTime(hour: $batchTutoringSessionCreationModel->getStartTime()->format('H'), minute: $batchTutoringSessionCreationModel->getStartTime()->format('i'));
                $endDateTime = clone $date->setTime(hour: $batchTutoringSessionCreationModel->getEndTime()->format('H'), minute: $batchTutoringSessionCreationModel->getEndTime()->format('i'));

                $turoringSession = (new TutoringSession())
                    ->setBuilding($batchTutoringSessionCreationModel->getBuilding())
                    ->setRoom($batchTutoringSessionCreationModel->getRoom())
                    ->setCreatedBy($user)
                    ->setTutoring($batchTutoringSessionCreationModel->getTutoring())
                    ->setStartDateTime($startDateTime)
                    ->setEndDateTime($endDateTime);
                $this->entityManager->persist($turoringSession);
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

    private function getAllDatesBetweenDatesByWeekdays(DateTimeInterface $startDate, DateTimeInterface $endDate, array $selectedWeekdays): array
    {
        $result = [];

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->format('N');

            if (in_array($dayOfWeek, $selectedWeekdays)) {
                $result[] = clone $currentDate;
            }
            if ($dayOfWeek > 4) {
                // Skip Saturday and Sunday
                $currentDate->modify(sprintf('+%s day', 8 - $dayOfWeek));
            } else {
                $currentDate->modify('+1 day');
            }
        }

        return $result;
    }
}
