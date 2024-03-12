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

    /**
     * Affiche le formulaire d'ajout d'une tâche
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ExperienceRepository $experienceRepository
     * @param ProjectRepository $projectRepository
     * @param integer $id d'une expérience ou d'un projet selon la source
     * @param string $source projet ou expérience
     * @return Response
     */
    #[Route('/add/{id}/{source}/', name: 'add', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
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
                $task->setExperience($experience);
            } elseif ($source === 'project') {
                $project = $projectRepository->find($id);
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




    /**
     * Supprime une tâche associée a une experience ou un projet
     *
     * @param EntityManagerInterface $manager
     * @param Task $task
     * @param string $source projet ou expérience
     * @return Response
     */
    #[Route('/{id}/{source}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
    #[IsGranted(TaskVoter::DELETE, subject: 'task')]
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
