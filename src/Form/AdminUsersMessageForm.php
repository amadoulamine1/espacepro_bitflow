<?php

namespace App\Form;

use App\Entity\Sfd;
use App\Entity\Users;
use App\Entity\Message;
use App\Form\MessageType;
use App\Entity\UsersMessage;
use App\Repository\SfdRepository;
use App\Repository\UsersRepository;
use App\Form\MessageUsersMessageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminUsersMessageForm extends AbstractType
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
            
            ->add('message',MessageUsersMessageType::class,[
                "attr"  => [
                    "class" => "form-control"
                ]
            ])
            ->add('choixProfil',ChoiceType::class,[
                'label'    => 'Message envoyé à ',
                'label_attr' => [
                    // Style the main label like an <h3>
                    'class' => 'mb-4 font-semibold text-gray-900 dark:text-white'
                ],
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
                    // Apply Flowbite classes for the UL-like container
                    'class' => 'items-center w-full text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg sm:flex dark:bg-gray-700 dark:border-gray-600 dark:text-white',
                    'data-controller' => 'choix-profil',
                ],
                'choice_attr' => function($choiceValue, $choiceKey, $choiceIndex) {
                    $attrs = [
                        // Apply Flowbite classes to the input checkbox
                        'class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500',
                    ];
                    if ($choiceValue === 'Tout le monde') {
                        $attrs['data-choix-profil-target'] = 'toutLeMonde';
                        $attrs['data-action'] = 'change->choix-profil#toggleAutresProfils';
                    } else {
                        $attrs['data-choix-profil-target'] = 'autreProfil';
                    }
                    return $attrs;
                },
            ])

            // Champ de sélection pour le mode de filtre (par SFD, par Réseaux ou Tout le monde)
            ->add('filterMode', ChoiceType::class, [
                'choices' => [
                    'Tout le monde' => 'all',
                    'Filtrer par SFD' => 'sfds',
                    'Filtrer par Réseau' => 'reseau',
                ],
                "label" => "nom",
                'label_attr' => [
                    'class' => 'label-form'
                ],
                //'placeholder' => 'Choisir un mode de filtre',
                'attr' => [
                    'id'=> 'filterMode',
                    'class' => 'form-control select2',
                  //  'onchange' => 'toggleFilterMode(this.value)', // Fonction JS déclenchée au changement
                ],
                'row_attr' => [
                     'id'=> 'filterMode', // Classe CSS personnalisée
                     'style' => 'display: flex; align-items: center; gap: 10px;',
                ],
                'mapped' => false,
                'required' => true,
            ])
            ->add('sfds', SfdAutocompleteField::class,[
                'mapped' => false,
               'multiple' => true, 
                'required' => false,
            ])

            ->add('reseau', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'placeholder' => 'Sélectionner un réseau',
                'attr' => [
                    'class' => 'block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ],
                'autocomplete' => true,
                'required' => false,
                'multiple' => true, // <-- IMPORTANT
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsersMessage::class,
        ]);
    }
}
