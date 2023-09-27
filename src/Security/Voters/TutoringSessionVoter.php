<?php

namespace App\Security\Voters;

use App\Entity\Student;
use App\Entity\TutoringSession;
use DateTime;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TutoringSessionVoter extends Voter
{
    final public const TUTOR_EDIT_TUTORING_SESSION = 'tutor-edit-tutoring-session';
    final public const TUTOR_DELETE_TUTORING_SESSION = 'tutor-delete-tutoring-session';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::TUTOR_EDIT_TUTORING_SESSION,
            self::TUTOR_DELETE_TUTORING_SESSION,
        ])) {
            return false;
        }

        return $subject instanceof TutoringSession;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof Student) {
            return false;
        }

        /** @var TutoringSession $tutoringSession */
        $tutoringSession = $subject;

        return match ($attribute) {
            self::TUTOR_EDIT_TUTORING_SESSION => in_array(Student::ROLE_TUTOR, $user->getRoles()) && $this->canEditTutoringSession($tutoringSession),
            self::TUTOR_DELETE_TUTORING_SESSION => in_array(Student::ROLE_TUTOR, $user->getRoles()) && $this->canDeleteTutoringSession($tutoringSession),
            default => throw new LogicException('This code should not be reached!'),
        };
    }

    private function canEditTutoringSession(TutoringSession $tutoringSession): bool
    {
        return $tutoringSession->getEndDateTime() > new DateTime();
    }

    private function canDeleteTutoringSession(TutoringSession $tutoringSession): bool
    {
        return $tutoringSession->getEndDateTime() > new DateTime();
    }
}
