<?php

namespace App\Model;

interface UserPasswordInterface
{
    public function getPlainPassword(): ?string;

    public function setPassword(?string $password): self;
}
