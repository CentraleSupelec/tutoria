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
class BatchTutoringSessionCreationModel
{
    #[Assert\Valid]
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

    #[Assert\NotNull]
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

    /**
     * @return DateTimeInterface
     */
    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    /**
     * @param DateTimeInterface $startTime
     */
    public function setStartTime(?DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    /**
     * @param DateTimeInterface $endTime
     */
    public function setEndTime(?DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    /**
     * @param DateTimeInterface $startDate
     */
    public function setStartDate(?DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    /**
     * @param DateTimeInterface $endDate
     */
    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return Building
     */
    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    /**
     * @param Building $building
     */
    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoom(): ?string
    {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function setRoom(?string $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSaveDefaultValues(): ?bool
    {
        return $this->saveDefaultValues;
    }

    /**
     * @param bool $saveDefaultValues
     */
    public function setSaveDefaultValues(?bool $saveDefaultValues): self
    {
        $this->saveDefaultValues = $saveDefaultValues;

        return $this;
    }
}
