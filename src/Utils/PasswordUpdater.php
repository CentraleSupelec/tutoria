<?php

namespace App\Utils;

use App\Model\UserPasswordInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUpdater
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function hashPassword(UserPasswordInterface $userPassword): void
    {
        $plainPassword = $userPassword->getPlainPassword();

        if ('' === (string) $plainPassword) {
            return;
        }

        $hashedPassword = $this->userPasswordHasher->hashPassword($userPassword, $userPassword->getPlainPassword());
        $userPassword->setPassword($hashedPassword);
        $userPassword->eraseCredentials();
    }
}
