<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\LtiToolUtils;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainRepositoryInterface;

class KeyChainManager implements KeyChainRepositoryInterface
{
    public function __construct(private readonly LtiToolUtils $ltiToolUtils)
    {
    }

    public function find(string $identifier): ?KeyChainInterface
    {
        return $this->ltiToolUtils->getTutorIAkeyChain();
    }

    public function findByKeySetName(string $keySetName): array
    {
        return [$this->ltiToolUtils->getTutorIAkeyChain()];
    }
}
