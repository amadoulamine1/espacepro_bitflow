<?php

namespace App\Form;

use App\Entity\Message;
use App\Entity\Users;
use App\Entity\UsersMessage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersMessageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('is_read')
            ->add('readAt')
            ->add('is_visibleBCEAO')
            ->add('is_accepted')
            ->add('motifRejet')
           /* ->add('sender', EntityType::class, [
                'class' => Users::class,
                'autocomplete' => true,
            ])*/
            ->add('sender', UsersAutocompleteField::class, [
              //  'multiple' => true,
            ])
            ->add('message', MessageForm::class)
            /*->add('recipient', EntityType::class, [
                'class' => Users::class,
                'autocomplete' => true,
            ])*/
            ->add('recipient', UsersAutocompleteField::class, [
                
            ])

            ->add('sfds',SfdAutocompleteField::class,[
                'multiple' => true  ,
                'tom_select_options' => [
                    'maxItems' => 3,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UsersMessage::class,
        ]);
    }
}
