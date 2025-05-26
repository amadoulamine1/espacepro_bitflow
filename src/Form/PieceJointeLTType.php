<?php

namespace App\Form;

use App\Entity\PieceJointe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PieceJointeLTType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
 
        //    ->add('libelle',TextType::class)
        //    ->add('type',TextType::class)
            
       
            ->add('file', VichFileType::class,[
                'label'=> 'Ajouter la lettre de transmission ici',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => '...',
                'download_uri' => 'string',
                'download_label' => 'download',
                'asset_helper' => true,
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PieceJointe::class,
        ]);
    }
}
