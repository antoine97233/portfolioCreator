<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/experiences', name: 'admin.experience.')]
class ExperienceController extends AbstractController
{

    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_USER')]
    public function index(ExperienceRepository $experienceRepository, TaskRepository $taskRepository): Response
    {

        // $user = $this->getUser();
        // $experiences = $experienceRepository->findBy(['user' => $user], ['end_date' => 'DESC']);

        $results = $experienceRepository->findAllWithTasksByUser();

        $experiences = [];
        $tasks = [];

        foreach ($results as $experience) {
            $experiences[] = $experience;
            $tasks[$experience->getId()] = $experience->getTask();
        }

        return $this->render('admin/experience/index.html.twig', [
            'controller_name' => 'HomeController',
            'experiences' => $experiences,
            'tasks' => $tasks,

        ]);
    }


    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('admin.experience.index');
        }

        return $this->render('admin/experience/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
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

            return $this->redirectToRoute('admin.experience.index');
        }

        return $this->render('admin/experience/edit.html.twig', [

            'form' => $form->createView()
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
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

        return $this->redirectToRoute('admin.experience.index');
    }
}
