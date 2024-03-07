<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ExperienceRepository;
use App\Repository\ProjectRepository;
use App\Security\Voter\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tasks', name: 'admin.task.')]
class TaskController extends AbstractController
{


    #[Route('/add/{id}/{source}/', name: 'add', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
    #[IsGranted(TaskVoter::ADD)]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        ExperienceRepository $experienceRepository,
        ProjectRepository $projectRepository,
        int $id,
        string $source
    ): Response {


        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($source === 'experience') {
                $experience = $experienceRepository->find($id);
                $this->denyAccessUnlessGranted(TaskVoter::ADD, $experience);
                $task->setExperience($experience);
            } elseif ($source === 'project') {
                $project = $projectRepository->find($id);
                $this->denyAccessUnlessGranted(TaskVoter::ADD, $project);
                $task->setProject($project);
            }

            $em->persist($task);
            $em->flush();

            $this->addFlash(
                'success',
                'New task added successfully'
            );

            if ($source === 'experience') {
                return $this->redirectToRoute('admin.experience.index');
            } elseif ($source === 'project') {
                return $this->redirectToRoute('admin.project.index');
            }
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Add',
            'table' => 'task'
        ]);
    }




    #[Route('/{id}/{source}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
    #[IsGranted(TaskVoter::EDIT, subject: 'task')]
    public function delete(
        EntityManagerInterface $manager,
        Task $task,
        string $source

    ): Response {


        $manager->remove($task);
        $manager->flush();

        $this->addFlash(
            'success',
            'Experience deleted successfully'
        );

        if ($source === 'experience') {
            return $this->redirectToRoute('admin.experience.index');
        } elseif ($source === 'project') {
            return $this->redirectToRoute('admin.project.index');
        }
    }
}
