<?php

namespace App\Repository;

use App\Entity\FonctionSfd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FonctionSfd>
 *
 * @method FonctionSfd|null find($id, $lockMode = null, $lockVersion = null)
 * @method FonctionSfd|null findOneBy(array $criteria, array $orderBy = null)
 * @method FonctionSfd[]    findAll()
 * @method FonctionSfd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FonctionSfdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FonctionSfd::class);
    }

//    /**
//     * @return FonctionSfd[] Returns an array of FonctionSfd objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FonctionSfd
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
