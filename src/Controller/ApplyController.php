<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Repository\ApplyRepository;
use App\Services\EntityServices\ApplyService;
use App\Services\ErrorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ApplyController extends AbstractController
{
    private ErrorService $errorService;
    private ApplyService $applyService;
    private ApplyRepository $applyRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ErrorService $errorService,
        ApplyService $applyService,
        ApplyRepository $applyRepository,
        SerializerInterface $serializer
    ) {
        $this->errorService = $errorService;
        $this->applyService = $applyService;
        $this->applyRepository = $applyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws \JsonException
     * @throws DatabaseException
     * @throws InvalidRequestException
     */
    public function create(Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsApplyRequest($request)) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsApplyRequest($request), JSON_THROW_ON_ERROR,),
                400
            );
        }

        $apply = $this->applyService->createApply($request);

        try {
            $this->applyRepository->create($apply);
        } catch (\Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                400
            );
        }
        return new JsonResponse(['message' => 'Apply created successfully', 'status' => '201']);
    }

    public function read(): JsonResponse
    {

    }

    public function update(): JsonResponse
    {

    }

    public function delete(): JsonResponse
    {

    }

    public function list(): JsonResponse
    {

    }
}