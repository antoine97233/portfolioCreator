<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Security\Voter\MediaVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/media', name: 'admin.media.')]
class MediaController extends AbstractController
{

    #[Route('/{id}/{source}/add', name: 'add', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
    public function add(
        #[CurrentUser] User $user,
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        Request $request,
        EntityManagerInterface $em,
        int $id,
        string $source,
    ): Response {

        if ($source === 'user') {
            if ($id !== $user->getId()) {
                throw new AccessDeniedException('You are not allowed to access this resource.');
            }
        } elseif ($source === 'project') {
            $project = $projectRepository->find($id);

            if ($user->getId() !== $project->getUser()->getId()) {
                throw new AccessDeniedException('You are not allowed to access this resource.');
            }
        }

        $media = new Media();

        $form = $this->createForm(MediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($source === 'user') {
                $data = $userRepository->find($id);
                $media->setUser($data);
            } elseif ($source === 'project') {
                $data = $projectRepository->find($id);
                $media->setProject($data);
            }
            $em->persist($media);
            $em->flush();

            $this->addFlash(
                'success',
                'New media added successfully'
            );

            if ($source === 'user') {
                return $this->redirectToRoute('admin.user.index', ['id' => $user->getId()]);
            } elseif ($source === 'project') {
                return $this->redirectToRoute('admin.project.index');
            }
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Add',
            'table' => 'Media'
        ]);
    }



    #[Route('/{id}/{source}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
    #[IsGranted(MediaVoter::DELETE, subject: 'media')]
    public function delete(
        #[CurrentUser] User $user,
        EntityManagerInterface $manager,
        Media $media,
        string $source
    ): Response {

        if ($source === 'user') {
            $media->setUser(null);
        } elseif ($source === 'project') {
            $media->setProject(null);
        }

        $manager->remove($media);
        $manager->flush();

        $this->addFlash(
            'success',
            'Image deleted successfully'
        );

        if ($source === 'user') {
            return $this->redirectToRoute('admin.user.index', ['id' => $user->getId()]);
        } elseif ($source === 'project') {
            return $this->redirectToRoute('admin.project.index');
        }
    }
}
