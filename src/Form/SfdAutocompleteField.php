<?php

namespace App\Form;

use App\Entity\Sfd;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class SfdAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Sfd::class,
            'placeholder' => 'Choisir une IMF',
            'query_builder' => function ($er) {
                return $er->createQueryBuilder('s')
                    ->select('s')
                    ->Where('s.is_actif = true')
                    ->groupBy('s.id')
                    ->orderBy('s.id', 'ASC');
            },
            // 'choice_label' => 'name',

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            // 'searchable_fields' => ['name'],

            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
