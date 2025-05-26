<?php

// src/Service/CustomDirectoryNamer.php
namespace App\Service;

use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
//use Symfony\Component\Security\Core\Security;

class CustomDirectoryNamer implements DirectoryNamerInterface
{
    /*private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }*/

    public function directoryName($object, PropertyMapping $mapping): string
    {
        /*$user = $this->security->getUser();
        $prefix="guichet";
        if($user){
            $sfd=$user->getSfd();
            if($sfd)
                $prefix=$sfd->getNumAgrement(); 
        }
       // $username = $user ? $user->getUsername() : 'guichet';
        
        $year = date('Y');
        $month = date('m');*/
        $year = $object->getYear();
        $month = $object->getMonth();
        $prefix=$object->getPrefix();

        return sprintf('%s/%s/%s', $prefix, $year, $month);
    }
}