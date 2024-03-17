<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                ['empty_data' => '']
            )->add(
                'description',
                TextareaType::class,
                [
                    'attr' => ['rows' => 8],
                    'empty_data' => ''
                ]
            )->add(
                'longDescription',
                TextareaType::class,
                [
                    'attr' => ['rows' => 12],
                    'empty_data' => ''
                ]
            )
            ->add(
                'link',
                TextType::class,
                [
                    'label' => 'External link',

                    'empty_data' => '',
                    'required' => false
                ]
            )
            ->add(
                'githubLink',
                TextType::class,
                [
                    'label' => 'Github link',
                    'empty_data' => '',
                    'required' => false
                ]
            )
            ->add('skill', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'title',
                'autocomplete' => true,
                'multiple' => true,

            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
