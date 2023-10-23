<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function add(Customer $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Customer $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function findOneByEmail(string $email): ?Customer
    {
        return $this->findOneBy([
            'email' => $email
        ]);
    }
}
