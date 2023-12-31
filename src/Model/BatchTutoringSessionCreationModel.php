<?php

namespace App\Model;

use App\Constants;
use App\Entity\Building;
use App\Entity\Tutoring;
use App\Validator\Constraints as AppAssert;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AppAssert\StartTimeEndTimeConstraint]
#[AppAssert\StartDateEndDateConstraint]
#[AppAssert\AllDefaultWeekdaysHaveAtLeastOneSessionConstraint]
class BatchTutoringSessionCreationModel
{
    #[Assert\NotNull]
    private ?Tutoring $tutoring = null;

    #[Assert\NotNull]
    #[Assert\Count(['min' => 1, 'minMessage' => 'validation.batch_tutoring_session_creation_model.weekdays_min_message'])]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableWeekdays'], multiple: true)]
    private array $weekDays = [];

    #[Assert\NotNull]
    private ?DateTimeInterface $startTime = null;

    #[Assert\NotNull]
    private ?DateTimeInterface $endTime = null;

    #[Assert\NotNull]
    private ?DateTimeInterface $startDate = null;

    #[Assert\NotNull]
    private ?DateTimeInterface $endDate = null;

    #[Assert\NotNull]
    private ?Building $building = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $room = null;

    #[Assert\NotNull]
    private bool $saveDefaultValues = false;

    public function getTutoring(): ?Tutoring
    {
        return $this->tutoring;
    }

    public function setTutoring(Tutoring $tutoring): self
    {
        $this->tutoring = $tutoring;

        return $this;
    }

    public function getWeekDays(): array
    {
        return $this->weekDays;
    }

    public function setWeekDays(array $weekDays): self
    {
        $this->weekDays = $weekDays;

        return $this;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getSaveDefaultValues(): ?bool
    {
        return $this->saveDefaultValues;
    }

    public function setSaveDefaultValues(?bool $saveDefaultValues): self
    {
        $this->saveDefaultValues = $saveDefaultValues;

        return $this;
    }
}
