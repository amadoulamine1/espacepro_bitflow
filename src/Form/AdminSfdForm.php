<?php

namespace App\Form;

use App\Entity\Sfd;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminSfdForm extends AbstractType
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
            ->add('reseau', TextType::class, [
                'required' => false,
                'attr' => [
                    'data-controller' => 'ux-autocomplete',
                    'data-ux-autocomplete-url-value' => '/admin/sfd/reseau/autocomplete',
                    'data-ux-autocomplete-min-chars-value' => 1,
                    'data-ux-autocomplete-tom-select-options-value' => '{"create":true}',
                ],
            ])
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
