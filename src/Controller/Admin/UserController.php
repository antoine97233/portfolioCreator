<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\DeleteUserType;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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

        return $this->render('/admin/form/form.html.twig', [
            'user' => $user,
            'form' => $form,
            'action' => 'Edit',
            'table' => 'profile'
        ]);
    }

    #[Route('/{id}/editpassword', name: 'password', methods: ['GET', 'POST'])]
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

                $manager->flush();

                return $this->redirectToRoute('home');
            } else {
                $this->addFlash(
                    'danger',
                    'Incorrect password'
                );
            }
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Edit',
            'table' => 'password'
        ]);
    }

    // ...


    // ...

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordHasherInterface $userPasswordHasher,
        TokenStorageInterface $tokenStorage // Ajoutez cette ligne
    ): Response {
        /** @var User $user */
        $user = $this->security->getUser();

        $form = $this->createForm(DeleteUserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            if ($userPasswordHasher->isPasswordValid($user, $plainPassword)) {

                $manager->remove($user);
                $manager->flush();

                $tokenStorage->setToken(null);

                $this->addFlash(
                    'success',
                    'Account deleted successfully'
                );

                return $this->redirectToRoute('home');
            } else {
                $this->addFlash(
                    'danger',
                    'Incorrect password'
                );
            }
        }

        return $this->render('admin/form/form.html.twig', [
            'form' => $form->createView(),
            'action' => 'Delete',
            'table' => 'account',
        ]);
    }
}
