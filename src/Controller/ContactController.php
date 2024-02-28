<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\User;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/{slug}-{id}/contact', name: 'contact', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'],)]
    public function contact(string $slug, int $id, EntityManagerInterface $em, Request $request, MailerInterface $mailer): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        if ($user->getSlug() !== $slug) {
            return $this->redirectToRoute('user', ['slug' => $user->getSlug(), 'id' => $user->getId()]);
        }

        $data = new ContactDTO();
        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $mail = (new TemplatedEmail())
                    ->to($user->getEmail())
                    ->from($data->email)
                    ->subject('Contact request')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context(['data' => $data]);

                $mailer->send($mail);
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
        return $this->render('contact/contact.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form
        ]);
    }
}
