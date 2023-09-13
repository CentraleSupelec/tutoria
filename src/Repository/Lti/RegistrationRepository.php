<?php

declare(strict_types=1);

namespace App\Repository\Lti;

use App\Entity\Lti\Registration;
use App\Exception\NonUniqueRegistrationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Registration>
 *
 * @method Registration|null find($id, $lockMode = null, $lockVersion = null)
 * @method Registration|null findOneBy(array $criteria, array $orderBy = null)
 * @method Registration[]    findAll()
 * @method Registration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Registration::class);
    }

    /**
     * @throws NonUniqueRegistrationException
     */
    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?Registration
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->join('r.platform', 'p')
            ->where('p.audience = :issuer')
            ->setParameter('issuer', $issuer)
        ;

        if (null !== $clientId) {
            $queryBuilder
                ->andWhere('r.clientId = :clientId')
                ->setParameter('clientId', $clientId)
            ;
        }

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new NonUniqueRegistrationException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
