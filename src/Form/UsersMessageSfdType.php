<?php

namespace App\Form;

use App\Entity\Users;
use App\Entity\Sfd;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\UsersMessage;
use App\Repository\UsersRepository;
use App\Repository\SfdRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersMessageSfdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('is_read')
           // ->add('readAt')
           /* ->add('sender',EntityType::class,[
                "class" => Users::class,
                "choice_label" => "nom",
                "choice_value" => "nom",
                "attr"  => [
                    "class" => "form-control"
                ] ,   
                'expanded' => false,
                'multiple' => false  ,
                'query_builder' => function(UsersRepository $repo) {
                       // return $repo->findAll();
                       return $repo->createQueryBuilder('u');
                                //    ->select('u')
                                //    ->distinct();
                }
   
            ])*/
            
            ->add('message',MessageUsersMessageType::class,[
               //  "class" => Message::class,
                 //"choice_label" => "titre",*/
                 "attr"  => [
                     "class" => "form-control"
                 ]      
   
            ])
            /*->add('choixProfil',ChoiceType::class,[
               'label'    => 'Message envoyé à ',
               'multiple' => true,
               'expanded' => true,
                
                'choices'  => [
                    'Tout le monde' => 'Tout le monde',
                    'PCA' => 'PCA',
                    'PCS' => 'PCS',
                    'PCC' => 'PCC',
                    'Gerant' => 'Gerant'
                ],  
                'choice_attr' => function() {
                return ['checked' => 'checked'];
                },
                "attr"  => [
                     "class" => "form-control form-check form-check-inline",
                     
                ]    
   
            ])*/
            /*->add('recipient',EntityType::class,[
                 "class" => Users::class,
                "choice_label" => "nom",
                "attr"  => [
                    "class" => "form-control"
                ] ,   
                'expanded' => false,
                'multiple' => false  ,
                'query_builder' => function(UsersRepository $repo) {
                       // return $repo->findAll();
                       return $repo->createQueryBuilder('u');
                                //    ->select('u')
                                //    ->distinct();
                }
   
            ])*/

            /* ->add('sfds',EntityType::class,[
                 "class" => Sfd::class,
                "choice_label" => "numAgrement",
                "attr"  => [
                    "class" => "form-control"
                ] ,   
                'expanded' => false,
                'multiple' => true  ,
                'query_builder' => function(SfdRepository $repo) {
                       // return $repo->findAll();
                       return $repo ->createQueryBuilder('sfd')
                                    ->where('sfd.is_actif = true')
                                    ->orderBy('sfd.numAgrement', 'ASC');
;
                                //    ->select('u')
                                //    ->distinct();
                }
   
            ])*/
/*->add('receipters',EntityType::class,[
                 "class" => Users::class,
                "choice_label" => "nom",
                "attr"  => [
                    "class" => "form-control"
                ] ,   
                'expanded' => false,
                'multiple' => true  ,
                'query_builder' => function(UsersRepository $repo) {
                       // return $repo->findAll();
                       return $repo->createQueryBuilder('u');
                                //    ->select('u')
                                //    ->distinct();
                }
   
            ])*/
            

            // ->add('receipters',EntityType::class,[
            //      "class" => Users::class,
            //     "choice_label" => "nom",
            //     "attr"  => [
            //         "class" => "form-control"
            //     ] ,   
            //     'expanded' => false,
            //     'multiple' => true  ,
            //     'query_builder' => function(UsersRepository $repo) {
            //            // return $repo->findAll();
            //            return $repo->createQueryBuilder('u');
            //                     //    ->select('u')
            //                     //    ->distinct();
            //     }
   
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsersMessage::class,
        ]);
    }
}
