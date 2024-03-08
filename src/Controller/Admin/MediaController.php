<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/media', name: 'admin.media.')]
class MediaController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    #[Route('/{id}/{source}/add', name: 'add', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS, 'source' => '.+'])]
    public function add(
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        Request $request,
        EntityManagerInterface $em,
        int $id,
        string $source,
    ): Response {

        /** @var User */
        $user = $this->security->getUser();

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
    public function delete(
        UserRepository $userRepository,
        ProjectRepository $projectRepository,
        EntityManagerInterface $manager,
        Media $media,
        string $source
    ): Response {

        /** @var User */
        $user = $this->security->getUser();


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
