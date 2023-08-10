<?php

namespace App\Model;

use App\Entity\Building;
use App\Entity\Tutoring;
use App\Validator\Constraints as AppAssert;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BatchSessionCreation
{
    #[Assert\NotBlank(allowNull: false)]
    private Tutoring $tutoring;

    #[Assert\NotNull]
    private bool $mondaySelected;

    #[Assert\NotNull]
    private bool $tuesdaySelected;

    #[Assert\NotNull]
    private bool $wednesdaySelected;

    #[Assert\NotNull]
    private bool $thursdaySelected;

    #[Assert\NotNull]
    private bool $fridaySelected;

    #[AppAssert\StartTimeEndTimeConstraint]
    private DateTimeInterface $startTime;

    #[AppAssert\StartTimeEndTimeConstraint]
    private DateTimeInterface $endTime;

    #[AppAssert\StartDateEndDateConstraint]
    private DateTimeInterface $startDate;

    #[AppAssert\StartDateEndDateConstraint]
    private DateTimeInterface $endDate;

    private ?Building $building = null;
    private ?string $room = null;

    public function getTutoring(): Tutoring
    {
        return $this->tutoring;
    }

    public function setTutoring(Tutoring $tutoring): self
    {
        $this->tutoring = $tutoring;

        return $this;
    }

    /**
     * @return bool
     */
    public function getMondaySelected(): ?bool
    {
        return $this->mondaySelected;
    }

    /**
     * @param bool $mondaySelected
     */
    public function setMondaySelected(?bool $mondaySelected): self
    {
        $this->mondaySelected = $mondaySelected;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTuesdaySelected(): ?bool
    {
        return $this->tuesdaySelected;
    }

    /**
     * @param bool $tuesdaySelected
     */
    public function setTuesdaySelected(?bool $tuesdaySelected): self
    {
        $this->tuesdaySelected = $tuesdaySelected;

        return $this;
    }

    /**
     * @return bool
     */
    public function getWednesdaySelected(): ?bool
    {
        return $this->wednesdaySelected;
    }

    /**
     * @param bool $wednesdaySelected
     */
    public function setWednesdaySelected(?bool $wednesdaySelected): self
    {
        $this->wednesdaySelected = $wednesdaySelected;

        return $this;
    }

    /**
     * @return bool
     */
    public function getThursdaySelected(): ?bool
    {
        return $this->thursdaySelected;
    }

    /**
     * @param bool $thursdaySelected
     */
    public function setThursdaySelected(?bool $thursdaySelected): self
    {
        $this->thursdaySelected = $thursdaySelected;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFridaySelected(): ?bool
    {
        return $this->fridaySelected;
    }

    /**
     * @param bool $fridaySelected
     */
    public function setFridaySelected(?bool $fridaySelected): self
    {
        $this->fridaySelected = $fridaySelected;

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
}
