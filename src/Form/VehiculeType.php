<?php

namespace App\Form;

use App\Entity\Vehicule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('marque', ChoiceType::class , [// menu déroulant dans les form]
                'choices' => [
                    'Peugeot' => 'Peugeot',
                    'Renault' => 'Renault',
                    'BMW' => 'BMW',
                    'Tesla' => 'Tesla'
                ]
            ])
            ->add('modele')
            ->add('description')
            ->add('photo' , FileType::class, [
                "mapped" => false, "required" => false
            ])
            ->add('prix_journalier')
            // ->add('date_enregistrement')
            ->add("save" , SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
