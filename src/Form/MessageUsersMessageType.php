<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\PieceJointe;
use App\Form\PieceJointeType;
use App\Form\TinyMCEEditableType;
use Symfony\Component\Form\AbstractType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MessageUsersMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lettreTransmission',PieceJointeLTType::class,[
                 'label' => 'Lettre de transmission',
            ])

            ->add('titre', TextType::class)
           /*->add('corps', TextareaType::class , [
                    'label' => 'Contenu du message',
                    'attr' => [
                        'class' => 'tinymce',
                        'rows' => 10, // Optionnel
                    ],
                ])*/
            /* ->add('corps', TinymceType::class , [
                    'label' => 'Contenu du message',
                    'attr' => [
                        'class' => 'tinymce',
                        'toolbar' => 'bold italic underline | bullist numlist',
                        'rows' => 10, // Optionnel
                    ],
            ])*/
           /* ->add('corps', TinyMCEEditableType::class, [
                'label' => 'Contenu du message',
                'required' => false,
            ])*/
            ->add('corps', TextareaType::class , [
                    'label' => 'Contenu du message',
                    'attr' => [
                        'class' => 'tiny-editable',
                        'rows' => 10, // Optionnel
                    ],
                ])
           /* ->add('pieceJointes', CollectionType::class, [
                'entry_type' => PieceJointeUsersMessageType::class,
                'allow_add' => true,
                'by_reference' => false,
                'prototype' => true,
                'attr' => array('class' => 'collection-piecejointes filepond-collection'),
            ]);*/
            ->add('pieceJointes', CollectionType::class, [
                'entry_type' => PieceJointeUsersMessageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
                'required' => false,
            ])
            // Add an event listener to filter out empty new PieceJointe entries
            //afin de nettoyer les pieces jointes vides
            ->get('pieceJointes')->addEventListener(
                FormEvents::SUBMIT,
                function (FormEvent $event) {
                    /** @var Collection|null $collection */
                    $collection = $event->getData();

                    if ($collection instanceof Collection) {
                        $toRemove = [];
                        foreach ($collection as $pieceJointe) {
                            if ($pieceJointe instanceof PieceJointe && $pieceJointe->getId() === null && $pieceJointe->getFile() === null) {
                                $toRemove[] = $pieceJointe;
                            }
                        }
                        foreach ($toRemove as $pjToRemove) {
                            $collection->removeElement($pjToRemove);
                        }
                    }
                }
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
            'multiple' => true,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'PieceJointeType';
    }
}
