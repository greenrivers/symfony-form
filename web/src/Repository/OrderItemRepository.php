<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 *
 * @method OrderItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderItem[]    findAll()
 * @method OrderItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    public function add(OrderItem $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(OrderItem $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }
}
