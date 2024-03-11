<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\User;
use App\Event\ContactRequestEvent;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/{slug}/{id}/contact', name: 'contact', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'],)]
    public function contact(
        string $slug,
        int $id,
        EntityManagerInterface $em,
        Request $request,
        EventDispatcherInterface $dispatcher
    ): Response {
        $user = $em->getRepository(User::class)->find($id);
        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        $data = new ContactDTO();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $dispatcher->dispatch(new ContactRequestEvent($data, $user));
                $this->addFlash(
                    'success',
                    'Email sent successfully'
                );
            } catch (Exception $e) {
                $this->addFlash(
                    'danger',
                    'Error : email failed to send'
                );
            }

            $this->redirectToRoute('contact', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }
        return $this->render('admin/form/form.html.twig', [
            'form' => $form,
            'action' => 'Contact',
            'table' => 'user'
        ]);
    }
}
