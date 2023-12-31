<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Manufacturer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manufacturer>
 *
 * @method Manufacturer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Manufacturer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Manufacturer[]    findAll()
 * @method Manufacturer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManufacturerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manufacturer::class);
    }

    public function add(Manufacturer $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Manufacturer $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function findOneByTaxId(string $taxId): ?Manufacturer
    {
        return $this->findOneBy([
            'taxId' => $taxId
        ]);
    }
}
