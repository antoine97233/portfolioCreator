<?php

namespace App\EventSubscriber;

use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailingSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly MailerInterface $mailer)
    {
    }


    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;
        $user = $event->user;

        $mail = (new TemplatedEmail())
            ->to($user->getEmail())
            ->from($data->email)
            ->subject('Contact request')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['data' => $data]);

        $this->mailer->send($mail);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
        ];
    }
}
