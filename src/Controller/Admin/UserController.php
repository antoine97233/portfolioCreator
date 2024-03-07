<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/user', name: 'admin.user.')]
class UserController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/{id}', name: 'index', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function index(
        UserRepository $userRepository,
        User $user,
    ): Response {

        $user = $userRepository->find($user);


        return $this->render('/admin/user/index.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
        ]);
    }



    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    #[IsGranted(UserVoter::EDIT, subject: 'user')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            $this->addFlash(
                'success',
                'Informations edited successfully'
            );

            return $this->redirectToRoute('user', ['id' => $user->getId(), 'slug' => $user->getSlug()]);
        }

        return $this->render('/admin/user/edit.html.twig', [
            'controller_name' => 'UserController',
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/{id}/editpassword', name: 'user.edit.password', methods: ['GET', 'POST'])]
    public function editPassword(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $userPasswordHasher,
        int $id
    ): Response {

        /** @var User $user */
        $user = $this->security->getUser();

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('plainPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();


            if ($userPasswordHasher->isPasswordValid($user, $oldPassword)) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $newPassword
                    )
                );

                $this->addFlash(
                    'success',
                    'Password edited successfully'
                );

                $manager->persist($user);
                $manager->flush($user);

                return $this->redirectToRoute('home');
            } else {
                $this->addFlash(
                    'danger',
                    'Incorrect password'
                );
            }
        }

        return $this->render('admin/user/editPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
