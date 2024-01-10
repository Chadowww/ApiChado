<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\ResumeRepository;
use App\Services\EntityServices\ResumeService;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ResumeController extends AbstractController
{
    private ErrorService $errorService;
    private ResumeRepository $resumeRepository;
    private ResumeService $resumeService;
    private SerializerInterface $serializer;

    public function __construct(
        ErrorService $errorService,
        ResumeRepository $resumeRepository,
        ResumeService $resumeService,
        SerializerInterface $serializer
    ) {
        $this->errorService = $errorService;
        $this->resumeRepository = $resumeRepository;
        $this->resumeService = $resumeService;
        $this->serializer = $serializer;
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     * @throws DatabaseException
     */
    public function create(Request $request, ImageController $imageController): JsonResponse
    {

        if ($this->errorService->getErrorsResumeRequest($request) !== []) {
            throw new InvalidRequestException(
                json_encode(
                    $this->errorService->getErrorsResumeRequest($request),
                    JSON_THROW_ON_ERROR),
                400
            );
        }
        $fileName = $imageController->create($request);
        $resume = $this->resumeService->createResume(
            $request,
            json_decode($fileName->getContent(), true, 512, JSON_THROW_ON_ERROR)['name']
        );
        try {
            $this->resumeRepository->create($resume);
        } catch (\Exception $e) {
            throw new DatabaseException(
                json_encode(
                    $this->errorService->getErrorsResumeRequest($request),
                    JSON_THROW_ON_ERROR),
                500
            );
        }
        return new JsonResponse('Resume created with success!', 201, [], true);
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function read(int $id): JsonResponse
    {
        try {
            $resume = $this->resumeRepository->read($id);
            if (!$resume) {
                throw new ResourceNotFoundException(json_encode(['Resume not found!'], JSON_THROW_ON_ERROR), 404);
            }
        } catch (\Exception $e) {
            throw new DatabaseException(json_encode(['Resume not found!'], JSON_THROW_ON_ERROR), 500);
        }
        return new JsonResponse($this->serializer->serialize($resume, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     * @throws DatabaseException
     */
    public function update(int $id, Request $request, ImageController $imageController): JsonResponse
    {
        if ($this->errorService->getErrorsResumeRequest($request) !== []) {
            throw new InvalidRequestException(
                json_encode(
                    $this->errorService->getErrorsResumeRequest($request),
                    JSON_THROW_ON_ERROR),
                400
            );
        }
        try {
            $resume = $this->resumeRepository->read($id);
            if (!$resume) {
                throw new ResourceNotFoundException(json_encode(['Resume not found!'], JSON_THROW_ON_ERROR), 404);
            }
        } catch (\Exception $e) {
            throw new DatabaseException(json_encode(['Resume not found!'], JSON_THROW_ON_ERROR), 500);
        }

        $fileName = json_decode($imageController->create($request)->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->resumeService->updateResume($resume, $request, $fileName['name']);

        try {
            $this->resumeRepository->update($resume);
        } catch (\Exception $e) {
            throw new DatabaseException(json_encode(['Resume not found!'], JSON_THROW_ON_ERROR), 500);
        }
        return new JsonResponse('Resume updated with success!', 200, [], true);
    }

    public function delete(): JsonResponse
    {

    }

    public function list(): JsonResponse
    {

    }
}