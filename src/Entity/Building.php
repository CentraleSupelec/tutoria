<?php

namespace App\Entity;

use App\Repository\BuildingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
class Building implements Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Assert\NotBlank(message: 'Veuillez saisir le nom du batiment.', allowNull: false)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'buildings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'building', targetEntity: Tutoring::class)]
    private Collection $tutorings;

    public function __construct()
    {
        $this->tutorings = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s @ %s', $this->getName(), $this->campus->getName());
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

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

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
            $tutoring->setBuilding($this);
        }

        return $this;
    }

    public function removeTutoring(Tutoring $tutoring): static
    {
        // set the owning side to null (unless already changed)
        if ($this->tutorings->removeElement($tutoring) && $tutoring->getBuilding() === $this) {
            $tutoring->setBuilding(null);
        }

        return $this;
    }
}
