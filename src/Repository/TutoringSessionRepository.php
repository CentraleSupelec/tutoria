<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\TutoringSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findByTutorings(array $tutorings): array
    {
        $queryBuilder = $this->createQueryBuilder('ts')
            ->andWhere('ts.tutoring IN (:tutorings)')
            ->setParameter('tutorings', $tutorings)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByTutored(Student $student): array
    {
        $queryBuilder = $this->createQueryBuilder('ts')
            ->andWhere(':tutored MEMBER OF ts.students')
            ->setParameter('tutored', $student)
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
