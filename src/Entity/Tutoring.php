<?php

namespace App\Entity;

use App\Repository\TutoringRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TutoringRepository::class)]
class Tutoring
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $room = null;

    #[ORM\ManyToOne(targetEntity: Building::class, inversedBy: 'tutorings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Building $building = null;

    #[ORM\OneToMany(mappedBy: 'tutoring', targetEntity: TutoringSession::class, orphanRemoval: true)]
    private Collection $tutoringSessions;

    #[ORM\OneToMany(mappedBy: 'tutoring', targetEntity: Student::class)]
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
            $student->setTutoring($this);
        }

        return $this;
    }

    public function removeTutor(Student $student): static
    {
        // set the owning side to null (unless already changed)
        if ($this->tutors->removeElement($student) && $student->getTutoring() === $this) {
            $student->setTutoring(null);
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