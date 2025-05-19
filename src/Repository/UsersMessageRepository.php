<?php

namespace App\Repository;

use App\Entity\UsersMessage;
use App\Entity\Users;
use App\Entity\Message;
use App\Repository\UsersRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\Users as EntityUsers;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method UsersMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsersMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsersMessage[]    findAll()
 * @method UsersMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersMessageRepository extends ServiceEntityRepository
{
    private  $guichet;
  //  private $requestStack;
    public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack)
    {
     //   $usersRepository = $doctrine->getRepository(Users::class);
        //dd($this->guichet);
        if ($requestStack->getCurrentRequest() != null)
            if ($requestStack->getCurrentRequest()->getSession() != null)
                $this->guichet = $requestStack->getCurrentRequest()->getSession()->get('guichet');
        //Si guichet =null , le renseigner et renseigner la session 
        if ($this->guichet === null) {
            $this->guichet = $doctrine->getRepository(Users::class)->findGuichet();
            if ($requestStack->getCurrentRequest() !== null && $requestStack->getCurrentRequest()->getSession() !== null) {
                $requestStack->getCurrentRequest()->getSession()->set('guichet', $this->guichet);
            }
        }
       // dump($this->guichet);
        //dump("construct");
        parent::__construct($doctrine, UsersMessage::class);
    }


    public function findAlls()
    {
      /*  return $this->createQueryBuilder('um')
            ->andWhere('u.id > 10')
            // ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            // ->setMaxResults(10)
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();*/
        //dump("findAlls",$this->guichet);
        return $this->createQueryBuilder('um')
           // ->join('um.sender', 'sender')
            ->andWhere('um.recipient = :recipient')
            ->setParameter('recipient', $this->guichet)
            ->orderBy('um.id', 'DESC')
            ->getQuery()
            ->setCacheable(true) // Active le cache
            ->getResult();

    }


    //Retourner tous les messages envoyes A un SFD
    public function findAllToSfd(Users $user)
    {
        return $this->findAllTo($user);
    }

    //Retourner tous les messages envoyes A un utilisateur
    public function findAllToWithAccepted(Users $user, bool $is_accepted)
    {
        return $this->createQueryBuilder('um')
            ->andWhere('um.recipient = :user')
            ->andWhere('um.is_accepted = :is_accepted')
            ->setParameter('user', $user)
            ->setParameter('is_accepted', $is_accepted)
            ->orderBy('um.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();
    }

    //Retourner tous les messages envoyes A un utilisateur
    public function findAllTo(Users $user)
    {
        return $this->createQueryBuilder('um')
            ->andWhere('um.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('um.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();
    }

    //Retourner tous les messages en attente de validation au guichet
    public function findMessageAttente()
    {
        return $this->findMessageValidation(null);
    }
    public function findMessageNonAttente()
    {
        return array_merge($this->findMessageAcceptes(), $this->findMessageRefuses());
    }


    //Retourner tous les messages refuses
    public function findMessageRefuses()
    {
        return $this->findMessageValidation(false);
    }

    //Retourner tous les messages Acceptes
    public function findMessageAcceptes()
    {
        return $this->findMessageValidation(true);
    }


    //Retourner tous les messages de validation
    public function findMessageValidation($is_accepted)
    {
        $val= $this->createQueryBuilder('um')
            ->andWhere('um.recipient = :user')
           // ->andWhere('um.is_accepted = :is_accepted')
            ->setParameter('user', $this->guichet)
           // ->setParameter('is_accepted', $is_accepted)
            ->orderBy('um.id', 'DESC');
        if ($is_accepted === null) {
            $val->andWhere('um.is_accepted IS NULL');
        } else {
            $val->andWhere('um.is_accepted = :is_accepted')
            ->setParameter('is_accepted', $is_accepted);
        }
            // ->setMaxResults(10)
          return $val->getQuery()
          //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();
    }

    //Retourner tous les messages envoyes Aux  SFD
    public function findAllFromSfdSent()
    {
        //dd($this->guichet);
        return $this->findAllFrom($this->guichet);
    }

    //Retourner tous les messages envoyes A un Sfd donné
    public function findAllFromASfdSent(Users $user)
    {
        // dd($this->guichet);
        return $this->findAllFrom($user);
    }

    //Retourner tous les messages envoyes Au Guichet et qui ont ete accepte
    public function findAllToGuichetAccepted()
    {
        //  dd();
        return $this->findAllToWithAccepted($this->guichet, true);
    }

    //Retourner tous les messages envoyes Au Guichet 
    public function findAllToGuichet()
    {
        //dd($this->guichet);
        return $this->findAllTo($this->guichet);
    }


    //Retourner tous les messages envoyes par un Utilisateur
    public function findAllFrom(Users $user)
    {

        return $this->createQueryBuilder('um')
            ->andWhere('um.sender = :user')
            ->setParameter('user', $user)
            ->orderBy('um.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();
    }


    //Rechercher tous les messages de l'utilisateur
    public function findAllFromUser(Users $user)
    {
        dump($user);
        return $this->createQueryBuilder('um')
            ->andWhere('um.recipient = :user')
            ->setParameter('user', $user)
            ->orderBy('um.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getResult();
    }

    // Compter les messages non lues
    public function getNonReadMessages(Users $user)
    {
        return $this->createQueryBuilder('um')
            ->select('count(um.id)')
            ->andWhere('um.recipient = :user')
            ->andWhere('um.is_read = :is_read')
            ->setParameter('user', $user)
            ->setParameter('is_read', false)
            //->orderBy('um.id', 'DESC')
            // ->setMaxResults(10)
            ->getQuery()
            //->enableResultCache(true, 3600, 'cache_ton_entity_list') 
            ->getSingleScalarResult();
    }
    // /**
    //  * @return UsersMessage[] Returns an array of UsersMessage objects
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
    public function findOneBySomeField($value): ?UsersMessage
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
