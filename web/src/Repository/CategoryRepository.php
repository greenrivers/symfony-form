<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function add(Category $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);

        if ($flush) {
            $entityManager->flush();
        }
    }

    public function findFirst(): ?Category
    {
        return $this->findOneBy([], [
            'id' => Criteria::ASC
        ]);
    }

    public function findOneByName(string $name): ?Category
    {
        return $this->findOneBy([
            'name' => $name
        ]);
    }
}
