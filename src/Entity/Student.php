<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student implements Stringable, UserInterface
{
    use TimestampableEntity;
    final public const ROLE_TUTOR = 'ROLE_TUTOR';
    final public const ROLE_TUTORED = 'ROLE_TUTORED';

    final public const ROLES = [
        self::ROLE_TUTOR => self::ROLE_TUTOR,
        self::ROLE_TUTORED => self::ROLE_TUTORED,
    ];

    public static function getPossibleRoles(): array
    {
        return self::ROLES;
    }

    #[Groups(['tutorings', 'tutoringSessions'])]
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Groups(['tutorings', 'tutoringSessions'])]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Groups(['tutorings', 'tutoringSessions'])]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Groups(['tutorings', 'tutoringSessions'])]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\Email(message: 'Veuillez saisir un email valide.')]
    #[Assert\NotBlank(message: 'Veuillez saisir un email.', allowNull: false)]
    private ?string $email = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $roles = [];

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $enabled = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $lastLoginAt = null;

    #[ORM\ManyToMany(targetEntity: Tutoring::class, mappedBy: 'tutors')]
    private Collection $tutorings;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: TutoringSession::class)]
    private Collection $ownedTutoringSessions;

    public function __construct()
    {
        $this->ownedTutoringSessions = new ArrayCollection();
        $this->tutorings = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getCompleteName();
    }

    public function getCompleteName(): string
    {
        if (null === $this->firstName && null === $this->lastName) {
            return $this->email;
        }

        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): static
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // $this->setPlainPassword(null);
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, Tutoring>
     */
    public function getTutorings(): Collection
    {
        return $this->tutorings;
    }

    public function addTutoring(Tutoring $tutoring): static
    {
        if (!$this->tutorings->contains($tutoring)) {
            $this->tutorings->add($tutoring);
            $tutoring->addTutor($this);
        }

        return $this;
    }

    public function removeTutoring(Tutoring $tutoring): static
    {
        // remove from the owning side (unless already removed)
        if ($this->tutorings->removeElement($tutoring) && $tutoring->getTutors()->contains($this)) {
            $tutoring->removeTutor($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TutoringSession>
     */
    public function getOwnedTutoringSessions(): Collection
    {
        return $this->ownedTutoringSessions;
    }

    public function addOwnedTutoringSessions(TutoringSession $tutoringSession): static
    {
        if (!$this->ownedTutoringSessions->contains($tutoringSession)) {
            $this->ownedTutoringSessions->add($tutoringSession);
            $tutoringSession->setCreatedBy($this);
        }

        return $this;
    }

    public function removeOwnedTutoringSessions(TutoringSession $tutoringSession): static
    {
        // set the owning side to null (unless already changed)
        if ($this->ownedTutoringSessions->removeElement($tutoringSession) && $tutoringSession->getCreatedBy() === $this) {
            $tutoringSession->setCreatedBy(null);
        }

        return $this;
    }
}
