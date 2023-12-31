<?php

namespace App\Entity;

use App\Constants;
use App\Repository\TutoringRepository;
use App\Validator\Constraints as AppAssert;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[AppAssert\StartTimeEndTimeConstraint(groups: ['Default'])]
#[ORM\Entity(repositoryClass: TutoringRepository::class)]
class Tutoring implements Stringable
{
    use TimestampableEntity;

    #[Groups(['tutorings'])]
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Groups(['tutorings', 'tutoringSessions'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['tutorings'])]
    #[Assert\NotBlank(allowNull: true, groups: ['Default', 'AdminTutoringGroup'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $defaultRoom = null;

    #[Groups(['tutorings'])]
    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Assert\Count(['min' => 1, 'minMessage' => 'validation.batch_tutoring_session_creation_model.weekdays_min_message'], groups: ['Default'])]
    #[Assert\Choice(callback: [Constants::class, 'getAvailableWeekdays'], multiple: true, groups: ['Default'])]
    private ?array $defaultWeekDays = [];

    #[Groups(['tutorings'])]
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTimeInterface $defaultStartTime = null;

    #[Groups(['tutorings'])]
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTimeInterface $defaultEndTime = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    #[AppAssert\AcademicYearConstraint(groups: ['Default', 'AdminTutoringGroup'])]
    private ?string $academicYear = null;

    #[Groups(['tutorings'])]
    #[ORM\ManyToOne(targetEntity: Building::class, inversedBy: 'tutorings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $defaultBuilding = null;

    #[Groups(['tutorings'])]
    #[ORM\OneToMany(mappedBy: 'tutoring', targetEntity: TutoringSession::class, orphanRemoval: true)]
    #[ORM\OrderBy(['startDateTime' => 'ASC'])]
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

    public function __toString(): string
    {
        return (string) $this->name;
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

    public function getDefaultRoom(): ?string
    {
        return $this->defaultRoom;
    }

    public function setDefaultRoom(?string $defaultRoom): static
    {
        $this->defaultRoom = $defaultRoom;

        return $this;
    }

    public function getDefaultStartTime(): ?DateTimeInterface
    {
        return $this->defaultStartTime;
    }

    public function setDefaultStartTime(DateTimeInterface $defaultStartTime): static
    {
        $this->defaultStartTime = $defaultStartTime;

        return $this;
    }

    public function getDefaultEndTime(): ?DateTimeInterface
    {
        return $this->defaultEndTime;
    }

    public function setDefaultEndTime(DateTimeInterface $defaultEndTime): static
    {
        $this->defaultEndTime = $defaultEndTime;

        return $this;
    }

    public function getAcademicYear(): ?string
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?string $academicYear): static
    {
        $this->academicYear = $academicYear;

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

    public function getDefaultBuilding(): ?Building
    {
        return $this->defaultBuilding;
    }

    public function setDefaultBuilding(?Building $defaultBuilding): static
    {
        $this->defaultBuilding = $defaultBuilding;

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
