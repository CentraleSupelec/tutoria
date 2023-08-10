<?php

namespace App\Service;

use App\Entity\TutoringSession;
use App\Model\BatchSessionCreation;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BatchSessionCreationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {
    }

    public function batchCreateSessions(BatchSessionCreation $batchSessionCreation): void
    {
        $user = $this->security->getUser();

        $selectedWeekdays = [];
        if ($batchSessionCreation->getMondaySelected()) {
            $selectedWeekdays[] = 1;
        }
        if ($batchSessionCreation->getTuesdaySelected()) {
            $selectedWeekdays[] = 2;
        }
        if ($batchSessionCreation->getWednesdaySelected()) {
            $selectedWeekdays[] = 3;
        }
        if ($batchSessionCreation->getThursdaySelected()) {
            $selectedWeekdays[] = 4;
        }
        if ($batchSessionCreation->getFridaySelected()) {
            $selectedWeekdays[] = 5;
        }
        /** @var DateTimeInterface[] $dates */
        $dates = $this->getAllDatesBetweenDatesByWeekdays($batchSessionCreation->getStartDate(), $batchSessionCreation->getEndDate(), $selectedWeekdays);

        foreach ($dates as $date) {
            $startDateTime = clone $date->setTime(hour: $batchSessionCreation->getStartTime()->format('H'), minute: $batchSessionCreation->getStartTime()->format('i'));
            $endDateTime = clone $date->setTime(hour: $batchSessionCreation->getEndTime()->format('H'), minute: $batchSessionCreation->getEndTime()->format('i'));

            $turoringSession = (new TutoringSession())
                ->setBuilding($batchSessionCreation->getBuilding())
                ->setRoom($batchSessionCreation->getRoom())
                ->setCreatedBy($user)
                ->setTutoring($batchSessionCreation->getTutoring())
                ->setStartDateTime($startDateTime)
                ->setEndDateTime($endDateTime);
            $this->entityManager->persist($turoringSession);
        }
        $this->entityManager->flush();
    }

    public function getAllDatesBetweenDatesByWeekdays(DateTimeInterface $startDate, DateTimeInterface $endDate, array $selectedWeekdays): array
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
