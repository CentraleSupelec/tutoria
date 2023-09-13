<?php

namespace App\Model\Lti;

use OAT\Library\Lti1p3Core\Security\Key\KeyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Key implements KeyInterface
{
    final public const TUTOR_IA_KEY_ID = 'tutoraIaKeyID';

    #[Assert\NotBlank(allowNull: false)]
    #[Assert\Regex(pattern: '/file:\/\//', match: true, message: 'Content should refrence a key file (Example : file://path/to/publicKey/public.key')]
    private ?string $content = null;

    #[Assert\Choice(callback: [self::class, 'getPossibleAlgorithms'], multiple: false)]
    #[Assert\NotNull]
    private ?string $algorithm = null;

    public static function getPossibleAlgorithms(): array
    {
        return [
            self::ALG_ES256 => self::ALG_ES256,
            self::ALG_ES384 => self::ALG_ES384,
            self::ALG_ES512 => self::ALG_ES512,
            self::ALG_HS256 => self::ALG_HS256,
            self::ALG_HS384 => self::ALG_HS384,
            self::ALG_HS512 => self::ALG_HS512,
            self::ALG_RS256 => self::ALG_RS256,
            self::ALG_RS384 => self::ALG_RS384,
            self::ALG_RS512 => self::ALG_RS512,
        ];
    }

    public function getIdentifier(): string
    {
        return self::TUTOR_IA_KEY_ID;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPassPhrase(): ?string
    {
        return null;
    }

    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    public function setAlgorithm(string $algorithm): static
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    public function isFromFile(): bool
    {
        return true;
    }

    public function isFromArray(): bool
    {
        return false;
    }

    public function isFromString(): bool
    {
        return false;
    }
}
