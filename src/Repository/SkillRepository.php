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
    // ...


    /**
     * Récupère les skills avec le nb d'associations par utilisateur, en prenant en compte le filtre isOpenToWork.
     *
     * @param bool $isOpenToWork
     * @return SkillWithCountDTO[]
     */
    public function findSkillsWithCount(?bool $isOpenToWork): array
    {
        $queryBuilder = $this->createQueryBuilder('s')
            ->select('NEW App\\DTO\\SkillWithCountDTO(s.id, s.title, COUNT(DISTINCT ss.user))')
            ->leftJoin('s.scoreSkills', 'ss');

        if ($isOpenToWork) {
            $queryBuilder->join('ss.user', 'u')
                ->andWhere('u.isOpenToWork = :isOpenToWork')
                ->setParameter('isOpenToWork', $isOpenToWork);
        }

        return $queryBuilder
            ->groupBy('s.id, s.title')
            ->orderBy('COUNT(DISTINCT ss.user)', 'DESC')
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
