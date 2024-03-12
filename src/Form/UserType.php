<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, [
                'empty_data' => '',
                'label' => "Full name",
                'required' => false
            ])
            ->add('title', TextType::class, [
                'empty_data' => '',
                'label' => "Job Title",
                'required' => false
            ])->add('subtitle', TextType::class, [
                'empty_data' => '',
                'label' => "Job Subtitle",
                'required' => false
            ])
            ->add('shortDescription', TextareaType::class, [
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'rows' => 3
                ]
            ])
            ->add('longDescription', TextareaType::class, [
                'empty_data' => '',
                'required' => false,
                'attr' => [
                    'rows' => 8
                ]
            ])
            ->add('tel', TextType::class, [
                'label' => "Phone number",
                'required' => false
            ])
            ->add('Linkedin', TextType::class, [
                'label' => "Linkedin URL",
                'required' => false
            ])
            ->add('isOpenToWork', CheckboxType::class, [
                'label' => "Are you open to work ?",
                'required' => false
            ])
            ->add('isVisible', CheckboxType::class, [
                'label' => "Do you want to be visible on the platform?",
                'required' => false
            ])
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
