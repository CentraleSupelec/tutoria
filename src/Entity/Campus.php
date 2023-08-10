<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CampusRepository::class)]
class Campus implements Stringable
{
    use TimestampableEntity;

    #[Groups(['api', 'tutorings'])]
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Groups(['api'])]
    #[Assert\NotBlank(message: 'Veuillez saisir le nom du campus.', allowNull: false)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['api'])]
    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: Building::class)]
    private Collection $buildings;

    public function __construct()
    {
        $this->buildings = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->getName();
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

    /**
     * @return Collection<int, Building>
     */
    public function getBuildings(): Collection
    {
        return $this->buildings;
    }

    public function addBuilding(Building $building): static
    {
        if (!$this->buildings->contains($building)) {
            $this->buildings->add($building);
            $building->setCampus($this);
        }

        return $this;
    }

    public function removeBuilding(Building $building): static
    {
        // set the owning side to null (unless already changed)
        if ($this->buildings->removeElement($building) && $building->getCampus() === $this) {
            $building->setCampus(null);
        }

        return $this;
    }
}
