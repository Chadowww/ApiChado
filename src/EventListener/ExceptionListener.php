<?php

namespace App\EventListener;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    /**
     * @throws \JsonException
     */
    #[AsEventListener]
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $message = json_decode($exception->getMessage(), false, 512, JSON_THROW_ON_ERROR);

        switch (true) {
            case $exception instanceof InvalidRequestException:
            case $exception instanceof ResourceNotFoundException:
            case $exception instanceof DatabaseException:
                $event->setResponse(new JsonResponse($message, $exception->getCode()));
                break;
            default:
                $event->setResponse(new JsonResponse('Internal server error', 500));
        }
    }
}