<?php

namespace App\Repository;

use App\Entity\ScoreSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScoreSkill>
 *
 * @method ScoreSkill|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreSkill|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreSkill[]    findAll()
 * @method ScoreSkill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreSkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScoreSkill::class);
    }




    public function findAllSkillsWithScoresByUser(int $userId): array
    {
        return $this->createQueryBuilder('scoreSkill')
            ->select('skill', 'scoreSkill', 'user')
            ->leftJoin('scoreSkill.skill', 'skill')
            ->leftJoin('scoreSkill.user', 'user')
            ->andWhere('user.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }




    //    /**
    //     * @return ScoreSkill[] Returns an array of ScoreSkill objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ScoreSkill
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
