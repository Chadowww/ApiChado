<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Repository\ApplyRepository;
use App\Services\EntityServices\ApplyService;
use App\Services\ErrorService;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;


/**
 * @OA\Tag(name="Apply")
 */
class ApplyController extends AbstractController
{
    /**
     * @var ErrorService
     */
    private ErrorService $errorService;
    /**
     * @var ApplyService
     */
    private ApplyService $applyService;
    /**
     * @var ApplyRepository
     */
    private ApplyRepository $applyRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param ErrorService $errorService
     * @param ApplyService $applyService
     * @param ApplyRepository $applyRepository
     * @param SerializerInterface $serializer
     */
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
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                400
            );
        }
        return new JsonResponse(['message' => 'Apply created successfully', 'status' => '201']);
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     */
    public function read(int $id): JsonResponse
    {
        try {
            $apply = $this->applyRepository->read($id);
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                400
            );
        }

        return new JsonResponse($this->serializer->serialize($apply, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException
     * @throws JsonException
     * @throws Exception
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsApplyRequest($request)) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsApplyRequest($request), JSON_THROW_ON_ERROR,),
                400
            );
        }

        $apply = $this->applyRepository->read($id);

        $apply = $this->applyService->updateApply($request, $apply);
    }

    /**
     * @throws DatabaseException
     * @throws JsonException
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->applyRepository->delete($id);
        } catch (Exception $e) {
            throw new DatabaseException(
                json_encode($e->getMessage(), JSON_THROW_ON_ERROR,),
                400
            );
        }
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $applies = $this->applyRepository->list();

        return new JsonResponse($this->serializer->serialize($applies, 'json'), 200, [], true);
    }
}