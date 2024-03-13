<?php


namespace App\EventListener;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class UniqueConstraintListener
{
    private ?string $errorMessage = null;

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof UniqueConstraintViolationException) {
            $this->errorMessage = 'Duplicate data';

            $response = new Response($this->errorMessage, 400);

            $event->setResponse($response);
        }
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
