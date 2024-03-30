<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findProjectsByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 's')
            ->leftJoin('p.skill', 's')
            ->where('p.user = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findProjectWithSkills(int $projectId): ?Project
    {
        return $this->createQueryBuilder('p')
            ->select('p', 's')
            ->leftJoin('p.skill', 's')
            ->andWhere('p.id = :projectId')
            ->setParameter('projectId', $projectId)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Récupère les projets appartenant à un utilisateur avec les tâches associées
     *
     * @param  mixed $userId
     * @return array
     */
    public function findProjectWithTasksAndSkillsByUser(int $userId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p', 't', 's')
            ->leftJoin('p.task', 't')
            ->leftJoin('p.skill', 's')
            ->andWhere('p.user = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
