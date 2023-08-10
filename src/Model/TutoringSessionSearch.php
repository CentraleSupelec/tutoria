<?php

namespace App\Model;

use App\Entity\Tutoring;
use Symfony\Component\Validator\Constraints as Assert;

class TutoringSessionSearch
{
    #[Assert\NotBlank(allowNull: false)]
    private Tutoring $tutoring;

    public function getTutoring(): Tutoring
    {
        return $this->tutoring;
    }

    public function setTutoring(Tutoring $tutoring): self
    {
        $this->tutoring = $tutoring;

        return $this;
    }
}
