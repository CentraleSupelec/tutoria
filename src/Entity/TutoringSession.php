<?php

namespace App\Entity;

use App\Repository\TutoringSessionRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TutoringSessionRepository::class)]
class TutoringSession
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'ownedTutoringSessions', targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Student $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $startDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTimeInterface $endDateTime = null;

    #[ORM\Column]
    private ?bool $isRemote = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $room = null;

    #[ORM\ManyToOne()]
    private ?Building $building = null;

    #[ORM\ManyToMany(targetEntity: Student::class)]
    #[ORM\JoinTable(name: 'tutoring_session_tutor')]
    private Collection $tutors;

    #[ORM\ManyToMany(targetEntity: Student::class)]
    #[ORM\JoinTable(name: 'tutoring_session_tutored')]
    private Collection $students;

    #[ORM\ManyToOne(inversedBy: 'tutoringSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tutoring $tutoring = null;

    public function __construct()
    {
        $this->tutors = new ArrayCollection();
        $this->students = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreatedBy(): ?Student
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Student $student): static
    {
        $this->createdBy = $student;

        return $this;
    }

    public function getStartDateTime(): ?DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(DateTimeInterface $startDateTime): static
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getEndDateTime(): ?DateTimeInterface
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(DateTimeInterface $endDateTime): static
    {
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function isIsRemote(): ?bool
    {
        return $this->isRemote;
    }

    public function setIsRemote(bool $isRemote): static
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): static
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getTutors(): Collection
    {
        return $this->tutors;
    }

    public function addTutor(Student $student): static
    {
        if (!$this->tutors->contains($student)) {
            $this->tutors->add($student);
        }

        return $this;
    }

    public function removeTutor(Student $student): static
    {
        $this->tutors->removeElement($student);

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        $this->students->removeElement($student);

        return $this;
    }

    public function getTutoring(): ?Tutoring
    {
        return $this->tutoring;
    }

    public function setTutoring(?Tutoring $tutoring): static
    {
        $this->tutoring = $tutoring;

        return $this;
    }
}
