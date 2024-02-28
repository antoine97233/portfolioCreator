<?php

namespace App\Form;

use App\Entity\Experience;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                ['empty_data' => '']
            )
            ->add(
                'location',
                TextType::class,
                ['empty_data' => '']
            )
            ->add('startDate', DateType::class)
            ->add('endDate', DateType::class, ['required' => false])
            ->add('isFormation', CheckboxType::class, ['required' => false])
            ->add('save', SubmitType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->autoEndDate(...));
    }

    public function autoEndDate(PreSubmitEvent $event): void
    {
        $data = $event->getData();


        if (empty($data['endDate'])) {
            $currentDate = new DateTimeImmutable();
            $data['endDate'] = $currentDate->format('Y-m-d');
            $event->setData($data);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Experience::class,
        ]);
    }
}
