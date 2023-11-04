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

        $response = match (get_class($exception)) {
            InvalidRequestException::class, DatabaseException::class, ResourceNotFoundException::class => new JsonResponse([
                'error' => json_decode($exception->getMessage(), false, 512, JSON_THROW_ON_ERROR)
            ], $exception->getCode()),
            default => new JsonResponse([
                'error' => 'Something went wrong'
            ], 500),
        };
        $event->setResponse($response);

    }
}