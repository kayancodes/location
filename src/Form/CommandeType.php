<?php

namespace App\Form;

use DateTime;
use App\Entity\User;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Twig\FiltreExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class CommandeType extends AbstractType
{

    public function __construct(private FiltreExtension $filter){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_heure_depart' , DateTimeType::class , [
                'widget' => 'single_text',
                "attr" => [
                    "min" => (new DateTime())->format("Y-m-d H:i"), // permet de desactiver les dates avanat aujourdhui dasn les calendriers
        
                ]
            ])
            ->add('date_heure_fin', DateTimeType::class , [
                'widget' => 'single_text',
                "attr" => [
                    "min" => (new DateTime())->format("Y-m-d H:i"), // permet de desactiver les dates avanat aujourdhui dasn les calendriers
                ]
            ])
            ->add('vehicule' , EntityType::class , [
                'class' => Vehicule::class,
                // 'choice_label' => 'titre',
                'choice_label' => function ($vehicule) {
                    return $vehicule->getTitre() ." - " .$this->filter->deviseFr($vehicule->getPrixJournalier());
                }
            ])
            ->add('user' , EntityType::class , [
                'class' => User::class,
                'choice_label' => 'pseudo',
            ])
            ->add('save' , SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}


