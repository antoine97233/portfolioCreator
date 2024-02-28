<?php

namespace App\Controller;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExperienceController extends AbstractController
{

    #[Route('/experiences', name: 'experience.index')]
    public function index(ExperienceRepository $experienceRepository): Response
    {

        // $user = $this->getUser();
        // $experiences = $experienceRepository->findBy(['user' => $user], ['end_date' => 'DESC']);

        $experiences = $experienceRepository->findAll();

        return $this->render('experience/index.html.twig', [
            'controller_name' => 'HomeController',
            'experiences' => $experiences
        ]);
    }


    #[Route('/experience/add', name: 'experience.add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $experience = new Experience;
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($experience);
            $em->flush();

            $this->addFlash(
                'success',
                'New experience added successfully'
            );

            return $this->redirectToRoute('experience.index');
        }

        return $this->render('experience/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/experience/{id}/edit', name: 'experience.edit', methods: ['GET', 'POST'])]
    public function edit(
        Experience $experience,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Experience edited successfully'
            );

            return $this->redirectToRoute('experience.index');
        }

        return $this->render('experience/edit.html.twig', [

            'form' => $form->createView()
        ]);
    }


    #[Route('/experience/{id}/delete', name: 'experience.delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $manager,
        Experience $experience
    ): Response {

        $manager->remove($experience);
        $manager->flush();

        $this->addFlash(
            'success',
            'Experience deleted successfully'
        );

        return $this->redirectToRoute('experience.index');
    }
}
