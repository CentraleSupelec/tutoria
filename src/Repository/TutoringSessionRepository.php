<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\TutoringSession;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TutoringSession>
 *
 * @method TutoringSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method TutoringSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method TutoringSession[]    findAll()
 * @method TutoringSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TutoringSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, TutoringSession::class);
    }

    public function findByTutorings(Collection $tutorings, ?Student $student): array
    {
        $queryBuilder = $this->createQueryBuilder('ts')
            ->andWhere('ts.tutoring IN (:tutorings)')
            ->andWhere('ts.startDateTime >= :now')
            ->andWhere(':tutee NOT MEMBER OF ts.students')
            ->orderBy('ts.startDateTime', 'ASC')
            ->setParameter('tutorings', $tutorings)
            ->setParameter('now', new DateTime())
            ->setParameter('tutee', $student)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function fetchAllTutoringSessionsWithFutureEndDate(?Student $student): array
    {
        $queryBuilder = $this->createQueryBuilder('ts')
            ->andWhere('ts.endDateTime >= :now')
            ->andWhere(':tutee NOT MEMBER OF ts.students')
            ->orderBy('ts.startDateTime', 'ASC')
            ->setParameter('now', new DateTime())
            ->setParameter('tutee', $student)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findIncomingSessionsByTutee(?Student $student): array
    {
        $queryBuilder = $this->createQueryBuilder('ts')
            ->andWhere(':tutee MEMBER OF ts.students')
            ->andWhere('ts.startDateTime >= :now')
            ->orderBy('ts.startDateTime', 'ASC')
            ->setParameter('tutee', $student)
            ->setParameter('now', new DateTime())
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findPastSessionsByTutee(?Student $student): array
    {
        $queryBuilder = $this->createQueryBuilder('ts')
            ->andWhere(':tutee MEMBER OF ts.students')
            ->andWhere('ts.startDateTime <= :now')
            ->orderBy('ts.startDateTime', 'ASC')
            ->setParameter('tutee', $student)
            ->setParameter('now', new DateTime())
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
