<?php

namespace App\Controller;

use App\Entity\Apply;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\ApplyRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use Exception;
use JsonException;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Apply")
 */
class ApplyController extends AbstractController
{
    private RequestValidatorService $requestValidatorService;
    /**
     * @var EntityBuilder
     */
    private EntityBuilder $entityBuilder;
    /**
     * @var ApplyRepository
     */
    private ApplyRepository $applyRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param RequestValidatorService $requestValidatorService
     * @param EntityBuilder $entityBuilder,
     * @param ApplyRepository $applyRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RequestValidatorService $requestValidatorService,
        EntityBuilder $entityBuilder,
        ApplyRepository $applyRepository,
        SerializerInterface $serializer
    ) {
        $this->requestValidatorService = $requestValidatorService;
        $this->entityBuilder = $entityBuilder;
        $this->applyRepository = $applyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws JsonException|DatabaseException|InvalidRequestException|
     * @OA\RequestBody(
     *      request="JobOffer",
     *      description="Job offer to create",
     *      required=true,
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *              property="candidateId",
     *              type="integer",
     *              example=2
     *          ),
     *          @OA\Property(
     *              property="resumeId",
     *              type="integer",
     *              example=7
     *          ),
     *          @OA\Property(
     *              property="jobofferId",
     *              type="integer",
     *              example=2
     *          ),
     *          @OA\Property(
     *              property="status",
     *              type="string",
     *              example="denied | pending | accepted"
     *          ),
     *          @OA\Property(
     *              property="message",
     *              type="string",
     *              example="Message de candidature"
     *          )
     *      )
     * )
     * @OA\Response(
     *       response=400,
     *       description="An error was found in request",
     *       @OA\JsonContent(
     *           type="string",
     *           example="Request must contain status, candidateId, resumeId and jobofferId fields"
     *       )
     *  )
     * @OA\Response(
     *      response=201,
     *      description="Apply created.",
     *      @OA\JsonContent(
     *          type="string",
     *          example="Created"
     *      )
     * )
     **/
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $apply = new Apply();

        $this->requestValidatorService->throwError400FromData($data, $apply);


        $this->entityBuilder->buildEntity($apply, $data);

        try {
            $this->applyRepository->create($apply);
        } catch (Exception $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse(['message' => 'Apply created successfully'], 201);
    }

    /**
     * @throws JsonException|Exception
     * @OA\Response(
     *     response=200,
     *     description="Apply found",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="applyId",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="status",
     *     type="string",
     *     example="denied | pending | accepted"
     *    ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Message de candidature"
     *   ),
     *     @OA\Property(
     *     property="candidateId",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="resumeId",
     *     type="integer",
     *     example=7
     *     ),
     *     @OA\Property(
     *     property="jobofferId",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     *    ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     *   )
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while retrieving the apply",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while retrieving the apply"
     *    )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply not found"
     *   )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Apply id",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example=1
     *     )
     * )
     */
    public function read(int $id): JsonResponse
    {
        $apply = $this->applyRepository->read($id);

        if (!$apply) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The apply with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($apply, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException|JsonException|DatabaseException|ResourceNotFoundException
     * @OA\RequestBody(
     *     request="JobOffer",
     *     description="Job offer to update",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="status",
     *     type="string",
     *     example="denied | pending | accepted"
     *   ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Message de candidature"
     *  ),
     *     @OA\Property(
     *     property="candidateId",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="resumeId",
     *     type="integer",
     *     example=7
     *     ),
     *     @OA\Property(
     *     property="jobofferId",
     *     type="integer",
     *     example=2
     *     )
     * )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Apply updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply updated"
     *  )
     * )
     * @OA\Response(
     *     response=400,
     *     description="An error was found in request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Request must contain status, candidateId, resumeId and jobofferId fields"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while updating the apply",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while updating the apply"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Apply id",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example=1
     *     )
     * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $apply = $this->applyRepository->read($id);

        if (!$apply) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The apply with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->requestValidatorService->throwError400FromData($data, $apply);

        $apply = $this->entityBuilder->buildEntity($apply, $data);

        try {
            $this->applyRepository->update($apply);
        } catch (Exception $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse(['message' => 'Apply updated successfully'], 200);
    }

    /**
     * @throws DatabaseException|ResourceNotFoundException|JsonException
     * @OA\Response(
     *     response=200,
     *     description="Apply deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply deleted"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while deleting the apply",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while deleting the apply"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Apply id",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example=1
     *     )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        if (!$this->applyRepository->read($id)) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The apply with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $this->applyRepository->delete($id);
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse(['message' => 'Apply deleted successfully'], 200);
    }

    /**
     * @return JsonResponse
     * @throws DatabaseException|JsonException|ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Apply list",
     *     @OA\JsonContent(
     *     type="array",
     *     @OA\Items(
     *     type="object",
     *     @OA\Property(
     *     property="applyId",
     *     type="integer",
     *     example=1
     *     ),
     *     @OA\Property(
     *     property="status",
     *     type="string",
     *     example="denied | pending | accepted"
     *   ),
     *     @OA\Property(
     *     property="message",
     *     type="string",
     *     example="Message de candidature"
     * ),
     *     @OA\Property(
     *     property="candidateId",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="resumeId",
     *     type="integer",
     *     example=7
     *     ),
     *     @OA\Property(
     *     property="jobofferId",
     *     type="integer",
     *     example=2
     *     ),
     *     @OA\Property(
     *     property="created_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     *   ),
     *     @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     example="2021-09-01T00:00:00+00:00"
     * )
     * )
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="An error occurred while retrieving the apply list",
     *     @OA\JsonContent(
     *     type="string",
     *     example="An error occurred while retrieving the apply list"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Apply list not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Apply list not found"
     * )
     * )
     */
    public function list(): JsonResponse
    {
        $applies = $this->applyRepository->list();

        if (!$applies) {
            throw new resourceNotFoundException(
                json_encode('Applies list was not found', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($applies, 'json'), 200, [], true);
    }
}