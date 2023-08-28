<?php

namespace App\Model;

use DateTimeInterface;
use EcPhp\CasBundle\Security\Core\User\CasUserInterface;

interface TutoringUserInterface extends CasUserInterface
{
    public function isEnabled(): bool;

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): self;
}
