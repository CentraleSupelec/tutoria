<?php

namespace App\Entity\Lti;

use App\Model\Lti\KeyChain;
use App\Model\Lti\Tool;
use App\Repository\Lti\RegistrationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration implements RegistrationInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private ?string $clientId = null;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false, referencedColumnName: 'id')]
    #[Assert\Valid]
    private Platform $platform;

    #[Assert\Valid]
    private ?Tool $tool = null;

    #[Assert\Valid]
    private ?KeyChain $toolKeyChain = null;

    #[ORM\Column(length: 255)]
    private string $deploymentId;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $platformJwksUrl = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return (string) $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function getTool(): ?Tool
    {
        return $this->tool;
    }

    public function setTool(?Tool $tool): static
    {
        $this->tool = $tool;

        return $this;
    }

    public function getDeploymentId(): ?string
    {
        return $this->deploymentId;
    }

    public function setDeploymentId(string $deploymentId): static
    {
        $this->deploymentId = $deploymentId;

        return $this;
    }

    public function getDeploymentIds(): array
    {
        return [$this->deploymentId];
    }

    public function hasDeploymentId(string $deploymentId): bool
    {
        return $deploymentId === $this->deploymentId;
    }

    public function getDefaultDeploymentId(): ?string
    {
        return $this->deploymentId;
    }

    public function getPlatformJwksUrl(): ?string
    {
        return $this->platformJwksUrl;
    }

    public function setPlatformJwksUrl(?string $platformJwksUrl): static
    {
        $this->platformJwksUrl = $platformJwksUrl;

        return $this;
    }

    public function getToolJwksUrl(): ?string
    {
        return null;
    }

    public function getPlatformKeyChain(): ?KeyChain
    {
        return null;
    }

    public function getToolKeyChain(): ?KeyChain
    {
        return $this->toolKeyChain;
    }

    public function setToolKeyChain(?KeyChain $toolKeyChain): static
    {
        $this->toolKeyChain = $toolKeyChain;

        return $this;
    }
}
