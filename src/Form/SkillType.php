<?php

namespace App\Form;

use App\Entity\Skill;
use App\Entity\User;
use App\Form\ScoreSkillType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('scoreSkills', CollectionType::class, [
                'entry_type' => ScoreSkillType::class,
                'allow_add' => true,  // Permet d'ajouter dynamiquement de nouveaux éléments à la collection
                'by_reference' => false,  // Oblige Symfony à utiliser les mutateurs pour chaque élément de la collection
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);
    }
}