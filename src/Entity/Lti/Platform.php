<?php

namespace App\Entity\Lti;

use App\Repository\Lti\PlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
class Platform implements PlatformInterface, Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(allowNull: false)]
    private ?string $audience = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $oidcAuthenticationUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $oAuth2AccessTokenUrl = null;

    #[ORM\OneToMany(mappedBy: 'platform', targetEntity: Registration::class)]
    private Collection $registrations;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return (string) $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAudience(): string
    {
        return $this->audience;
    }

    public function setAudience(string $audience): static
    {
        $this->audience = $audience;

        return $this;
    }

    public function getOidcAuthenticationUrl(): ?string
    {
        return $this->oidcAuthenticationUrl;
    }

    public function setOidcAuthenticationUrl(?string $oidcAuthenticationUrl): static
    {
        $this->oidcAuthenticationUrl = $oidcAuthenticationUrl;

        return $this;
    }

    public function getOAuth2AccessTokenUrl(): ?string
    {
        return $this->oAuth2AccessTokenUrl;
    }

    public function setOAuth2AccessTokenUrl(?string $oAuth2AccessTokenUrl): static
    {
        $this->oAuth2AccessTokenUrl = $oAuth2AccessTokenUrl;

        return $this;
    }

    /**
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): static
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations->add($registration);
            $registration->setPlatform($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): static
    {
        // set the owning side to null (unless already changed)
        if ($this->registrations->removeElement($registration) && $registration->getPlatform() === $this) {
            $registration->setPlatform(null);
        }

        return $this;
    }
}
