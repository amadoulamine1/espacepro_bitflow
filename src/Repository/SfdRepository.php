<?php

namespace App\Repository;

use App\Entity\Sfd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sfd|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sfd|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sfd[]    findAll()
 * @method Sfd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SfdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sfd::class);
    }

     /**
     * Récupère la liste des réseaux uniques de tous les SFD actifs
     *
     * @return array
     */
    public function findUniqueReseaux(): array
    {
        $query = $this->createQueryBuilder('sfd')
            ->select('DISTINCT sfd.reseau')
            ->where('sfd.reseau IS NOT NULL')
            ->andWhere('sfd.is_actif = true')
            ->orderBy('sfd.reseau', 'ASC')
            ->getQuery();
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') ;

        // Extraction en tableau simple
        return array_column($query->getResult(), 'reseau');
    }

    /**
     * Récupère la liste des SFD actifs
     *
     * @return array
     */
    public function findAllActive(): array{
        return $this->createQueryBuilder('sfd')
            ->where('sfd.is_actif = true')
            ->orderBy('sfd.numAgrement', 'ASC')
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();
    }

    /**
     * la liste des sfd qui ont leur reseau compris dans une liste entre en parametre
     *
     * @param array $reseaux
     * @return array
     */
    public function findByReseaux(array $reseaux): array
    {
        return $this->createQueryBuilder('sfd')
            ->where('sfd.reseau IN (:reseaux)')
            ->andWhere('sfd.is_actif = true')
            ->setParameter('reseaux', $reseaux)
            ->orderBy('sfd.numAgrement', 'ASC')
            // ->enableResultCache(3600, 'cache_ton_entity_list') // 1h de cache
            ->getQuery()
            ->getResult();
    }

       /**
     * Finds suggestions for a given field and query.
     *
     * @param string $field The entity field to search in (e.g., 'sigle', 'nomDeveloppe').
     * @param string $query The search query.
     * @param int $limit The maximum number of suggestions to return.
     * @return array A simple array of suggestion strings.
     */
    public function findSuggestions(string $field, string $query, int $limit = 10): array
    {
        // Basic validation for allowed fields to prevent SQL injection
        $allowedFields = ['sigle', 'nomDeveloppe', 'email', 'numAgrement']; // Add other fields if needed
        if (!in_array($field, $allowedFields)) {
            return [];
        }

        $qb = $this->createQueryBuilder('s')
            ->select('DISTINCT s.' . $field)
            ->where('s.' . $field . ' LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('s.' . $field, 'ASC')
            ->setMaxResults($limit);

        $results = $qb->getQuery()->getResult();

        // Transform the result into a simple array of strings
        return array_column($results, $field);
    }
    // /**
    //  * @return Sfd[] Returns an array of Sfd objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sfd
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
