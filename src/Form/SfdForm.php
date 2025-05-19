<?php

namespace App\Form;

use App\Entity\Sfd;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SfdForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numAgrement')
            ->add('sigle')
            ->add('nomDeveloppe')
            ->add('email')
            ->add('telephone')
            ->add('is_actif')
            ->add('type')
            ->add('decisionAgrement')
            ->add('region')
            ->add('reseau')
            ->add('is_article44')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sfd::class,
        ]);
    }
}
