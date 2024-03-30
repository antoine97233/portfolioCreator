<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use function Symfony\Component\String\u;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }



    /**
     * Récupère les utilisateurs possédant toutes les compétences spécifiées.
     *
     * @param array $skillIds
     * @param bool $isVisible
     *
     * @return Query
     */
    // ...

    public function findUserBySkills(array $skillIds, bool $isVisible, ?bool $isOpenToWork): Query
    {
        $queryBuilder = $this->createQueryBuilder('u');

        foreach ($skillIds as $key => $skillId) {
            $alias = 'sk' . $key;
            $queryBuilder
                ->join('u.scoreSkills', $alias)
                ->andWhere($alias . '.skill = :skillId' . $key)
                ->andWhere($alias . '.score IS NOT NULL') // Ajout pour s'assurer que l'utilisateur a un score pour cette compétence
                ->setParameter('skillId' . $key, $skillId);
        }

        $queryBuilder
            ->andWhere('u.isVisible = :isVisible')
            ->setParameter('isVisible', $isVisible);

        if ($isOpenToWork !== null) {
            $queryBuilder
                ->andWhere('u.isOpenToWork = :isOpenToWork')
                ->setParameter('isOpenToWork', $isOpenToWork);
        }

        return $queryBuilder->getQuery();
    }



    // ...

    /**
     * Récupère les utilisateurs avec les skills qui ont le statut openToWork.
     *
     * @param array $skillIds
     * @param bool $isOpenToWork
     * @param bool $isVisible
     *
     * @return array
     */
    public function findUserBySkillsAndOpenToWork(array $skillIds, bool $isOpenToWork, bool $isVisible): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.scoreSkills', 'ss')
            ->join('ss.skill', 'sk')
            ->where('u.isVisible = :isVisible')
            ->andWhere('sk.id IN (:skillIds)')
            ->andWhere('u.isOpenToWork = :isOpenToWork')
            ->setParameter('isVisible', $isVisible)
            ->setParameter('skillIds', $skillIds)
            ->setParameter('isOpenToWork', $isOpenToWork)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les utilisateurs qui ont choisi d'être visible.
     *
     * @param bool $isVisible
     * @param bool $isOpenToWork
     * 
     * @return Query
     */
    public function findUserVisible(bool $isVisible, bool $isOpenToWork): Query
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm')
            ->leftJoin('u.media', 'm')
            ->andWhere('u.isVisible = :isVisible')
            ->setParameter('isVisible', $isVisible)
            ->andWhere('u.isOpenToWork = :isOpenToWork')
            ->setParameter('isOpenToWork', $isOpenToWork)
            ->setMaxResults(4)
            ->setFirstResult(0)
            ->getQuery();
    }

    /**
     * Récupère les utilisateurs qui ont choisi d'être visible.
     *
     * @param bool $isVisible
     * @param bool $isOpenToWork
     *
     * @return Query
     */
    public function findUserByOpentoWorkandVisible(bool $isVisible, bool $isOpenToWork): Query
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm')
            ->leftJoin('u.media', 'm')
            ->andWhere('u.isVisible = :isVisible')
            ->andWhere('u.isOpenToWork = :isOpenToWork')
            ->setParameter('isVisible', $isVisible)
            ->setParameter('isOpenToWork', $isOpenToWork)
            ->setMaxResults(5)
            ->setFirstResult(0)
            ->getQuery();
    }



    /**
     * Récupère un utilisateur avec toutes les données qui lui appartiennent.
     *
     * @param int $userId
     *
     * @return User|null
     */
    public function findUserWithAll(int $userId): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm', 'e', 't', 'ss', 's', 'p', 'mp')
            ->leftJoin('u.media', 'm')
            ->leftJoin('u.experiences', 'e')
            ->leftJoin('e.task', 't')
            ->leftJoin('u.projects', 'p')
            ->leftJoin('p.media', 'mp')
            ->leftJoin('u.scoreSkills', 'ss')
            ->leftJoin('ss.skill', 's')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Récupère un utilisateur avec toutes les données qui lui appartiennent.
     *
     * @param int $userId
     *
     * @return User|null
     */
    public function findUserforApi(int $userId): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm', 'ss', 's', 'p', 'mp')
            ->leftJoin('u.media', 'm')
            ->leftJoin('u.projects', 'p')
            ->leftJoin('p.media', 'mp')
            ->leftJoin('u.scoreSkills', 'ss')
            ->leftJoin('ss.skill', 's')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }




    /**
     * Récupère un utilisateur par son email ou son username
     *
     * @param  mixed $usernameOrEmail
     * @return User
     */
    public function findUserByEmailOrUsername(string $usernameOrEmail): ?User
    {
        return $this->createQueryBuilder("u")
            ->Where("u.email = :identifier OR u.username= :identifier")
            ->andWhere("u.isVerified = true")
            ->setParameter("identifier", $usernameOrEmail)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }



    /**
     * @return User[]
     */
    public function findBySearchQuery(string $query): array
    {
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('u');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('u.fullname LIKE :f_' . $key)
                ->setParameter('f_' . $key, '%' . $term . '%');
        }

        /** @var User[] $result */
        $result = $queryBuilder
            ->orderBy('u.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * Transforms the search string into an array of search terms.
     *
     * @return string[]
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim()->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, static function ($term) {
            return 2 <= $term->length();
        });
    }



    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
