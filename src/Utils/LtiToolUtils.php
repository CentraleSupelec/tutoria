<?php

namespace App\Utils;

use App\Model\Lti\Key;
use App\Model\Lti\KeyChain;
use App\Model\Lti\Tool;

class LtiToolUtils
{
    final public const TUTOR_IA_KEY_SET_NAME = 'tutorIaKeySet';
    final public const TUTOR_IA_TOOL_NAME = 'TutorIa';
    final public const OIDC_INTIATION_PATH = 'lti/oidc/initiation';
    final public const TOOL_LAUNCH_PATH = 'lti/launch';

    public function __construct(private readonly string $publicKeyPath, private readonly string $privateKeyPath, private readonly string $hostUrl)
    {
    }

    public function getTutorIAkeyChain(): KeyChain
    {
        $privateKey = (new Key())->setAlgorithm(Key::ALG_RS256)->setContent($this->privateKeyPath);
        $publicKey = (new Key())->setAlgorithm(Key::ALG_RS256)->setContent($this->publicKeyPath);

        return (new KeyChain())->setKeySetName(self::TUTOR_IA_KEY_SET_NAME)
            ->setPrivateKey($privateKey)
            ->setPublicKey($publicKey)
        ;
    }

    public function getTutorIATool(): Tool
    {
        return (new Tool())->setName(self::TUTOR_IA_TOOL_NAME)
            ->setAudience($this->hostUrl)
            ->setOidcInitiationUrl(sprintf('%s/%s', $this->hostUrl, self::OIDC_INTIATION_PATH))
            ->setLaunchUrl(sprintf('%s/%s', $this->hostUrl, self::TOOL_LAUNCH_PATH))
        ;
    }
}
