<?php

namespace App\Entity;

use App\Repository\AcademicLevelRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AcademicLevelRepository::class)]
class AcademicLevel implements Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[Assert\NotBlank(message: 'Veuillez saisir le nom du niveau scolaire en français.', allowNull: false)]
    #[ORM\Column(length: 255)]
    private ?string $nameFr = null;

    #[Assert\NotBlank(message: 'Veuillez saisir le nom du niveau scolaire en anglais.', allowNull: false)]
    #[ORM\Column(length: 255)]
    private ?string $nameEn = null;

    public function __toString(): string
    {
        return (string) $this->getNameFr();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    public function setNameFr(string $nameFr): static
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): static
    {
        $this->nameEn = $nameEn;

        return $this;
    }
}
