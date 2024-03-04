<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
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

    public function findAllWithCount(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findBySkills(array $skillIds): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.skills', 's')
            ->andWhere('s.id IN (:skillIds)')
            ->setParameter('skillIds', $skillIds)
            ->getQuery()
            ->getResult();
    }



    public function findVisible(bool $isVisible): array
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm')
            ->leftJoin('u.media', 'm')
            ->where('u.isVisible = :isVisible')
            ->setParameter('isVisible', $isVisible)
            ->setMaxResults(2)
            ->setFirstResult(0)
            ->getQuery()
            ->getResult();
    }


    public function findUserWithMedia(int $userId): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm')
            ->leftJoin('u.media', 'm')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }



    public function findOpenToWork(bool $isOpenToWork): array
    {
        return $this->createQueryBuilder("o")
            ->andWhere("o.isOpenToWork = :isOpenToWork")
            ->setParameter("isOpenToWork", $isOpenToWork)
            ->getQuery()
            ->getResult();
    }

    public function findByEmailOrUsername(string $usernameOrEmail): ?User
    {
        return $this->createQueryBuilder("u")
            ->Where("u.email = :identifier OR u.username= :identifier")
            ->andWhere("u.isVerified = true")
            ->setParameter("identifier", $usernameOrEmail)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function paginateUsers(int $page): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('r'),
            $page,
            4
        );
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



    // public function paginateUsers(int $page, int $limit): Paginator
    // {
    //     $query = $this->createQueryBuilder('u')
    //         ->leftJoin('u.media', 'm')
    //         ->andWhere('u.isVisible = :isVisible')
    //         ->setParameter('isVisible', true)
    //         ->setFirstResult(($page - 1) * $limit)
    //         ->setMaxResults($limit)
    //         ->getQuery()
    //         ->setHint(Paginator::HINT_ENABLE_DISTINCT, false);

    //     return new Paginator($query, false);
    // }




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
