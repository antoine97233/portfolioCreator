<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use App\Entity\User;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use App\Security\Voter\ExperienceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/experiences', name: 'admin.experience.')]
class ExperienceController extends AbstractController
{


    /**
     * Affiche la liste des expériences et des tâches associées
     *
     * @param User $user
     * @param ExperienceRepository $userRepository
     * @return Response
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        ExperienceRepository $experienceRepository
    ): Response {

        $results = $experienceRepository->findExperienceWithTasksByUser($user->getId());

        $experiences = [];
        $tasks = [];

        foreach ($results as $experience) {
            $experiences[] = $experience;
            $tasks[$experience->getId()] = $experience->getTask();
        }

        return $this->render('admin/experience/index.html.twig', [
            'experiences' => $experiences,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]


    /**
     * Affiche le formulaire d'ajout d'expérience
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $experience = new Experience();
        $form = $this->createForm(ExperienceType::class, $experience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $experience->setUser($user);

            $em->persist($experience);
            $em->flush();

            $this->addFlash(
                'success',
                'New experience added successfully'
            );

            return $this->redirectToRoute('admin.experience.index');
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Add',
            'table' => 'experience'
        ]);
    }


    /**
     * Affiche le formulaire d'ajout d'expérience
     *
     * @param Experience $experience
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ExperienceVoter::EDIT, subject: 'experience')]
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

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit',
            'table' => 'experience'
        ]);
    }

    /**
     * Supprime une expérience
     *
     * @param EntityManagerInterface $em
     * @param Experience $experience
     * @return Response
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ExperienceVoter::DELETE, subject: 'experience')]
    public function delete(EntityManagerInterface $em, Experience $experience): Response
    {
        $em->remove($experience);
        $em->flush();

        $this->addFlash(
            'success',
            'Experience deleted successfully'
        );

        return $this->redirectToRoute('admin.experience.index');
    }
}
