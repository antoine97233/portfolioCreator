<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
                'empty_data' => ''
            ])
            ->add('slug', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('thumbnailFile', FileType::class)
            ->add('title', TextType::class, [
                'empty_data' => ''
            ])
            ->add('subtitle', TextType::class, [
                'empty_data' => ''
            ])
            ->add('shortDescription', TextType::class, [
                'empty_data' => ''
            ])
            ->add('longDescription', TextType::class, [
                'empty_data' => ''
            ])
            ->add('isOpenToWork')
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('fullname'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
