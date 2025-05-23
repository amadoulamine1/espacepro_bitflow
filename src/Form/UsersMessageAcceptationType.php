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

class UsersMessageAcceptationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('is_accepted', ChoiceType::class, [
                'choices'  => [
                    'En attente' => null,
                    'Accepter' => true,
                    'Refuser' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                "label" => "Acceptation",
                "attr"  => [
                    "class" => "checkbox-inline checkbox-switch form-check-inline "
                ],
                "row_attr"  => [
                    "class" => "checkbox-inline checkbox-switch form-check-inline"
                ],
                /*'attr'  => [
                    'class' => 'form-check-inline',
                ],
                'row_attr'  => [
                    'class' => 'form-check form-check-inline',
                ],*/
            ])
            ->add('motif_rejet', TextareaType::class, [
                "attr"  => [
                    "class" => "form-control"
                ],
                "required" => false,
                'empty_data' => '',
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UsersMessage::class,
        ]);
    }
}
