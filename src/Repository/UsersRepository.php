<?php

namespace App\Repository;

use App\Entity\Sfd;
use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    //public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * return l'utilisateur du Sfd selon le profil choisi
     */
    public function findByProfile(Sfd $sfd,string $fonction): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.sfd = :sfd')
            ->andWhere('u.fonction= :fonction')
            ->setParameter('sfd', $sfd)
            ->setParameter('fonction', $fonction)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * return le guichet
     */
    public function findGuichet(): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.fonction= :fonction')
            ->andWhere('u.nom= :nom')
            ->andWhere('u.prenom= :prenom')
            ->setParameter('fonction', "GUICHET")
            ->setParameter('nom', "guichet")
            ->setParameter('prenom', "guichet")
            ->getQuery()
            //->enableResultCache(true, 36000, 'guichet') 
            ->getOneOrNullResult()
        ;
    }

    /**Retourne tous les users Sfd  */

    public function findAllUsersSfd(): ?array{
        return $this->createQueryBuilder('u')
                ->andWhere('u.sfd IS NOT NULL')
                ->andWhere('u.is_actif= true')
                ->getQuery()
                ->getResult();
    }

    public function searchByTerm(string $term)
        {
            return $this->createQueryBuilder('u')
                ->where('CONCAT(u.nom, \' \', u.prenom) LIKE :term')
                ->orWhere('u.email LIKE :term')
                ->orWhere('u.username LIKE :term')
                ->setParameter('term', '%'.$term.'%')
                ->orderBy('u.nom', 'ASC')
                ->getQuery();
        }
    // /**
    //  * @return Users[] Returns an array of Users objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
