<?php

namespace App\Model\Lti;

use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use Symfony\Component\Validator\Constraints as Assert;

class KeyChain implements KeyChainInterface
{
    final public const TUTOR_IA_KEY_CHAIN_ID = 'tutorIaKeyChainID';

    #[Assert\NotBlank(allowNull: false)]
    private ?string $keySetName = null;

    #[Assert\Valid]
    private Key $publicKey;

    #[Assert\Valid]
    private ?Key $privateKey = null;

    public function getIdentifier(): string
    {
        return self::TUTOR_IA_KEY_CHAIN_ID;
    }

    public function getKeySetName(): string
    {
        return $this->keySetName;
    }

    public function setKeySetName(string $keySetName): static
    {
        $this->keySetName = $keySetName;

        return $this;
    }

    public function getPublicKey(): Key
    {
        return $this->publicKey;
    }

    public function setPublicKey(Key $publicKey): static
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    public function getPrivateKey(): ?Key
    {
        return $this->privateKey;
    }

    public function setPrivateKey(?Key $privateKey): static
    {
        $this->privateKey = $privateKey;

        return $this;
    }
}
