<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class UsersAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Users::class,
            'placeholder' => 'Choisis un destinataire',
            'choice_label' => function(Users $user) {
                return sprintf('%s %s (%s)', $user->getPrenom(), $user->getNom(), $user->getEmail());
            },

            // choose which fields to use in the search
            // if not passed, *all* fields are used
            'searchable_fields' => ['prenom', 'nom', 'email'],
            // 'query_builder' => function(UsersRepository $er) {
            //     return $er->createQueryBuilder('user')->andWhere('user.is_actif = :is_actif')->setParameter('is_actif', true);
            // }
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
