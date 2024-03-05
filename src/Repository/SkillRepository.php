<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Skill>
 *
 * @method Skill|null find($id, $lockMode = null, $lockVersion = null)
 * @method Skill|null findOneBy(array $criteria, array $orderBy = null)
 * @method Skill[]    findAll()
 * @method Skill[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }



    /**
     *
     * @return SkillWithCountDTO[]
     */
    public function findAllWithCount(): array
    {

        return $this->createQueryBuilder('s')
            ->select('NEW App\\DTO\\SkillWithCountDTO(s.id, s.title, COUNT(u.id))')
            ->leftJoin('s.user', 'u')
            ->groupBy('s.id')
            ->orderBy('COUNT(u.id)', 'DESC')
            ->getQuery()
            ->getResult();
    }



    public function findAllByUserWithScore(int $userId): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.user', 'u')
            ->andWhere('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }





    //    /**
    //     * @return Skill[] Returns an array of Skill objects
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

    //    public function findOneBySomeField($value): ?Skill
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
