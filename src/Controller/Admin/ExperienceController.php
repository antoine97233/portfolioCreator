<?php

namespace App\Controller\Admin;

use App\Entity\User as AppUser;
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

    #[Route('/', name: 'index')]
    #[IsGranted(ExperienceVoter::LIST)]
    public function index(
        ExperienceRepository $experienceRepository,
    ): Response {

        /** @var UserInterface $user */
        $user = $this->security->getUser();
        $userId = $user->getId();

        $canListAll = $this->security->isGranted(ExperienceVoter::LIST_ALL);

        if ($canListAll) {
            $results = $experienceRepository->findAll();
        } else {
            $results = $experienceRepository->findAllWithTasksByUser($userId);
        }

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
    #[IsGranted(ExperienceVoter::ADD)]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): Response {

        /** @var AppUser $user */
        $user = $this->security->getUser();

        $experience = new Experience;
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

        return $this->render('admin/experience/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

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

        return $this->render('admin/experience/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function delete(
        EntityManagerInterface $em,
        Experience $experience
    ): Response {

        $em->remove($experience);
        $em->flush();

        $this->addFlash(
            'success',
            'Experience deleted successfully'
        );

        return $this->redirectToRoute('admin.experience.index');
    }
}
