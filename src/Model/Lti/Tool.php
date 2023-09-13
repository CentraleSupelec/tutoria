<?php

namespace App\Model\Lti;

use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Tool implements ToolInterface
{
    final public const TUTOR_IA_TOOL_ID = 'tutorIaToolID';

    #[Assert\NotBlank(allowNull: false)]
    private ?string $name = null;

    #[Assert\NotBlank(allowNull: false)]
    private ?string $audience = null;

    #[Assert\NotBlank(allowNull: false)]
    private ?string $oidcInitiationUrl = null;

    private ?string $launchUrl = null;

    public function getIdentifier(): string
    {
        return self::TUTOR_IA_TOOL_ID;
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

    public function getOidcInitiationUrl(): string
    {
        return $this->oidcInitiationUrl;
    }

    public function setOidcInitiationUrl(string $oidcInitiationUrl): static
    {
        $this->oidcInitiationUrl = $oidcInitiationUrl;

        return $this;
    }

    public function getLaunchUrl(): ?string
    {
        return $this->launchUrl;
    }

    public function setLaunchUrl(?string $launchUrl): static
    {
        $this->launchUrl = $launchUrl;

        return $this;
    }

    public function getDeepLinkingUrl(): ?string
    {
        return null;
    }
}
