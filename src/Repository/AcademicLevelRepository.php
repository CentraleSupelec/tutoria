<?php

namespace App\Repository;

use App\Entity\AcademicLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AcademicLevel>
 *
 * @method AcademicLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcademicLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcademicLevel[]    findAll()
 * @method AcademicLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademicLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, AcademicLevel::class);
    }
}
