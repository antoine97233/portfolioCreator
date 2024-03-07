<?php

namespace App\Form;

use App\Entity\Media;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\SluggerInterface;

class FormListenerFactory
{

    private $managerRegistry;


    public function __construct(private SluggerInterface $slugger, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry->getManager();
    }

    public function autoSlug(string $field): callable
    {

        return function (PreSubmitEvent $event) use ($field) {

            $data = $event->getData();

            if (empty($data['slug'])) {
                $data['slug'] = strtolower($this->slugger->slug($data[$field]));
                $event->setData($data);
            }
        };
    }


    public function timestamps(): callable
    {
        return function (PostSubmitEvent $event) {

            $data = $event->getData();
            $data->setUpdatedAt(new DateTimeImmutable());
        };
    }

    public function removeMedia()
    {
        return function (PostSubmitEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $delete = $form->get('thumbnailFile')->get('delete')->getData();


            if ($data instanceof Media && $data->getId() !== null && $delete) {
                $data->getUser()->setMedia(null);
                $this->managerRegistry->remove($data);
                $this->managerRegistry->flush();
            }
        };
    }
}
