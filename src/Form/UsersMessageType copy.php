<?php

namespace App\Form;

use App\Entity\Sfd;
use App\Entity\Users;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\UsersMessage;
use App\Repository\SfdRepository;
use App\Repository\UsersRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UsersMessageType extends AbstractType
{

    private SfdRepository $sfdRepository;

    public function __construct(SfdRepository $sfdRepository)
    {
        $this->sfdRepository = $sfdRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // Récupération dynamique des réseaux uniques depuis la base de données
        $reseaux = $this->sfdRepository->findUniqueReseaux();

        // Conversion des réseaux sous forme de tableau associatif pour ChoiceType
        $choices = array_combine($reseaux, $reseaux);

        $builder
            //->add('is_read')
           // ->add('readAt')
          /*  ->add('sender',EntityType::class,[
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

           /* ->add('test', TextareaType::class , [
                    'label' => 'Contenu du message',
                    'attr' => [
                        'class' => 'tinymce',
                        'rows' => 10, // Optionnel
                    ],
                    'mapped' => false,
                ])*/
          
                    //deepseek
            ->add('choixProfil',ChoiceType::class,[
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
                'attr' => [
                    'class' => 'd-flex flex-wrap gap-5 p-0', // Réduction de l'espacement
                    'onchange' => 'disableAutresChoix()',
                ],
                'row_attr' => [
                    'class' => 'compact-check-group' // Classe CSS personnalisée
                ],
            ])

            // Champ de sélection pour le mode de filtre (par SFD, par Réseaux ou Tout le monde)
            ->add('filterMode', ChoiceType::class, [
                'choices' => [
                    'Tout le monde' => 'all',
                    'Filtrer par SFD' => 'sfd',
                    'Filtrer par Réseau' => 'reseau',
                ],
                "label" => "nom",
                //'placeholder' => 'Choisir un mode de filtre',
                'attr' => [
                    'id'=> 'filterMode',
                    'class' => 'form-control select2',
                    'onchange' => 'toggleFilterMode(this.value)', // Fonction JS déclenchée au changement
                ],
                'row_attr' => [
                     'id'=> 'filterMode', // Classe CSS personnalisée
                ],
                'mapped' => false,
                'required' => true,
            ])

            ->add('recipient',EntityType::class,[
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
            ])

            ->add('sfds',EntityType::class,[
                "class" => Sfd::class,
                "choice_label"  => function(Sfd $sfd) {
                                        return (string) $sfd;
                                    },
                "attr"  => [
                    'id'=> 'sfds',
                    "class" => "form-control select2 js-example-basic-multiple",
                    'data-select-all' => 'true', // Ajout d'un attribut pour activer "Sélectionner tout"
                ] ,
                'expanded' => false,
                'multiple' => true  ,
                'disabled' => true, // Désactivation du champ par défaut
                'row_attr' => [
                     'id'=> 'sfds', // Classe CSS personnalisée
                ],
                'query_builder' => function(SfdRepository $repo) {
                        return $repo ->createQueryBuilder('sfd')
                                    ->where('sfd.is_actif = true')
                                    ->orderBy('sfd.numAgrement', 'ASC');
                }
            ])
            // Sélection dynamique des réseaux uniques
            ->add('reseau', ChoiceType::class, [
                'choices' => array_filter($choices), 
                'placeholder' => 'Sélectionner un réseau',
                'attr' => [
                    'id'=> 'reseau',
                    'class' => 'form-control select2 js-example-basic-multiple',
                ],
                'expanded' => false,
                'multiple' => true  ,
                'required' => false,
                'disabled' => 'disabled', // Désactivation du champ par défaut
                'mapped' => false,
            ])
            ->add('receipters',EntityType::class,[
                "class" => Users::class,
                "choice_label" => "nom",
                "attr"  => [
                    "class" => "form-control"
                ] ,
                'expanded' => false,
                'multiple' => true  ,
                'query_builder' => function(UsersRepository $repo) {
                    return $repo->createQueryBuilder('u');
                }
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsersMessage::class,
        ]);
    }
}
