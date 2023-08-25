<?php

namespace App\Model;

use App\Entity\Tutoring;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class TutoringSessionSearch
{
    #[Assert\All([new Assert\Type(Tutoring::class)])]
    #[Assert\Valid]
    private Collection $tutorings;

    public function __construct()
    {
        $this->tutorings = new ArrayCollection();
    }

    public function getTutorings(): Collection
    {
        return $this->tutorings;
    }

    public function setTutorings(Collection $tutorings): self
    {
        $this->tutorings = $tutorings;

        return $this;
    }
}
