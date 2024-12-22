<?php

namespace App\Repository;

use App\Entity\Exercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercise>
 *
 * @method Exercise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exercise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exercise[]    findAll()
 * @method Exercise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    public function save(Exercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Exercise $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Exercise[] Returns an array of Exercise objects by difficulty
     */
    public function findByDifficulty(int $difficulty): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.difficulty = :difficulty')
            ->setParameter('difficulty', $difficulty)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Exercise[] Returns an array of Exercise objects for a specific chapter
     */
    public function findByChapter($chapterId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.chapter = :chapterId')
            ->setParameter('chapterId', $chapterId)
            ->orderBy('e.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
