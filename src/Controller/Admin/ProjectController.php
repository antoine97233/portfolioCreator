<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/projects', name: 'admin.project.')]
class ProjectController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        #[CurrentUser] User $user,
        ProjectRepository $projectRepository
    ): Response {

        $projects = $projectRepository->findAllWithTasksByUser($user->getId());

        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/add', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $project->setUser($user);

            $em->persist($project);
            $em->flush();

            $this->addFlash(
                'success',
                'New project added successfully'
            );

            return $this->redirectToRoute('admin.project.index');
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Add',
            'table' => 'project'
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ProjectVoter::EDIT, subject: 'project')]
    public function edit(
        Project $project,
        Request $request,
        EntityManagerInterface $em
    ): Response {

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Project edited successfully'
            );

            return $this->redirectToRoute('admin.project.index');
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit',
            'table' => 'project'

        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(ProjectVoter::DELETE, subject: 'project')]
    public function delete(
        EntityManagerInterface $em,
        Project $project
    ): Response {

        $em->remove($project);
        $em->flush();

        $this->addFlash(
            'success',
            'Project deleted successfully'
        );

        return $this->redirectToRoute('admin.project.index');
    }
}
