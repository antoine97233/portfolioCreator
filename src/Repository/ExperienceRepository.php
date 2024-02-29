<?php

namespace App\Repository;

use App\Entity\Experience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Experience>
 *
 * @method Experience|null find($id, $lockMode = null, $lockVersion = null)
 * @method Experience|null findOneBy(array $criteria, array $orderBy = null)
 * @method Experience[]    findAll()
 * @method Experience[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Experience::class);
    }


    public function findAllWithTasksByUser(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.task', 't')
            ->orderBy('e.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // public function findAllWithTasksByUser(User $user): array
    // {
    //     return $this->createQueryBuilder('e')
    //         ->leftJoin('e.tasks', 't') // Supposons que la propriété de relation dans Experience est appelée "tasks"
    //         ->leftJoin('e.user', 'u')  // Supposons que la propriété de relation dans Experience est appelée "user"
    //         ->andWhere('u = :user')
    //         ->setParameter('user', $user)
    //         ->orderBy('e.end_date', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }




    //    /**
    //     * @return Experience[] Returns an array of Experience objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Experience
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
