<?php

namespace App\Controller\Admin;

use App\Entity\Experience;
use App\Form\ExperienceType;
use App\Repository\ExperienceRepository;
use App\Security\Voter\ExperienceVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/experiences', name: 'admin.experience.')]
class ExperienceController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted(ExperienceVoter::LIST)]
    public function index(ExperienceRepository $experienceRepository): Response
    {

        /** @var UserInterface $user */
        $user = $this->security->getUser();
        $userId = $user->getId();


        $results = $experienceRepository->findAllWithTasksByUser($userId);


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
    #[IsGranted(ExperienceVoter::ADD)]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->security->getUser();

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

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ExperienceVoter::EDIT, subject: 'experience')]
    public function edit(Experience $experience, Request $request, EntityManagerInterface $em): Response
    {
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

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ExperienceVoter::EDIT, subject: 'experience')]
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
