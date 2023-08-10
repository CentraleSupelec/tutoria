<?php

namespace App\Entity;

use App\Repository\TutoringRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TutoringRepository::class)]
class Tutoring
{
    use TimestampableEntity;

    #[Groups(['tutorings'])]
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Groups(['tutorings'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['tutorings'])]
    #[Assert\NotBlank(allowNull: true)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $room = null;

    #[Groups(['tutorings'])]
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Assert\NotNull]
    private ?array $defaultWeekDays = [];

    #[Groups(['tutorings'])]
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTimeInterface $startTime = null;

    #[Groups(['tutorings'])]
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTimeInterface $endTime = null;

    #[Groups(['tutorings'])]
    #[ORM\ManyToOne(targetEntity: Building::class, inversedBy: 'tutorings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    #[Groups(['tutorings'])]
    #[ORM\OneToMany(mappedBy: 'tutoring', targetEntity: TutoringSession::class, orphanRemoval: true)]
    private Collection $tutoringSessions;

    #[Groups(['tutorings'])]
    #[ORM\ManyToMany(targetEntity: Student::class, inversedBy: 'tutorings')]
    private Collection $tutors;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcademicLevel $academicLevel = null;

    public function __construct()
    {
        $this->tutors = new ArrayCollection();
        $this->tutoringSessions = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(string $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getDefaultWeekDays(): ?array
    {
        return $this->defaultWeekDays;
    }

    public function setDefaultWeekDays(?array $defaultWeekDays): self
    {
        $this->defaultWeekDays = $defaultWeekDays;

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
     * @return Collection<int, TutoringSession>
     */
    public function getTutoringSessions(): Collection
    {
        return $this->tutoringSessions;
    }

    public function addTutoringSession(TutoringSession $tutoringSession): static
    {
        if (!$this->tutoringSessions->contains($tutoringSession)) {
            $this->tutoringSessions->add($tutoringSession);
            $tutoringSession->setTutoring($this);
        }

        return $this;
    }

    public function removeTutoringSession(TutoringSession $tutoringSession): static
    {
        // set the owning side to null (unless already changed)
        if ($this->tutoringSessions->removeElement($tutoringSession) && $tutoringSession->getTutoring() === $this) {
            $tutoringSession->setTutoring(null);
        }

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
            $student->addTutoring($this);
        }

        return $this;
    }

    public function removeTutor(Student $student): static
    {
        // remove from the owning side (unless already removed)
        if ($this->tutors->removeElement($student) && $student->getTutorings()->contains($this)) {
            $student->removeTutoring($this);
        }

        return $this;
    }

    public function getAcademicLevel(): ?AcademicLevel
    {
        return $this->academicLevel;
    }

    public function setAcademicLevel(?AcademicLevel $academicLevel): static
    {
        $this->academicLevel = $academicLevel;

        return $this;
    }
}
