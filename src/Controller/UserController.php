<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/user/{id}/edit', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaFile = $form->get('mediaFile')->getData();

            if ($mediaFile) {
                $media = new Media();
                $media->setThumbnailFile($mediaFile);
                $media->setUser($user);
                $em->persist($media);
            }

            $em->flush();

            $this->addFlash(
                'success',
                'Informations edited successfully'
            );

            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'form' => $form
        ]);
    }



    #[Route('/media/add', name: 'media.add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {

        $media = new Media();


        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($media);

            $em->flush();

            $this->addFlash(
                'success',
                'Media added successfully'
            );

            return $this->redirectToRoute('home');
        }



        return $this->render('user/media.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form,
            'media' => $media
        ]);
    }
}
