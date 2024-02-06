<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Services\RequestValidator\RequestValidatorService;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\ContractRepository;
use OpenApi\Annotations as OA;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Contract")
 */
class ContractController extends AbstractController
{
    private RequestValidatorService $requestValidatorService;
    private ContractRepository $contractRepository;
    private SerializerInterface $serializer;

    public function __construct(
        RequestValidatorService $requestValidatorService,
        ContractRepository $contractRepository,
        SerializerInterface $serializer,
    )
    {
        $this->requestValidatorService = $requestValidatorService;
        $this->contractRepository = $contractRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     * @OA\Response(
     *     response=201,
     *     description="Contract created",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Created"
     *   )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     *  )
     * )
     * @OA\RequestBody(
     *     request="Contract",
     *     description="Contract to create",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="type",
     *     type="string",
     *     example="CDI"
     *    )
     *  )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $contract = new Contract();

        $this->requestValidatorService->throwError400FromData($data, $contract);

        $contract->setType($request->get('type'));

        try {
            $this->contractRepository->create($contract);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse(['message' => 'Contract created successfully'], 201);
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     * @throws ResourceNotFoundException
     * @OA\Response(
     *     response=200,
     *     description="Contract read",
     *     @OA\JsonContent(
     *     type="string",
     *     example="{
     *     ""id"": 1,
     *     ""type"": ""CDI""
     * }"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Contract not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="the contract with id 1 was not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the contract",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function read(int $id): JsonResponse
    {
        $contract = $this->contractRepository->read($id);

        if (!$contract) {
            throw new ResourceNotFoundException(
                json_encode('the contract with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($contract, 'json'), 200, [], true);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     * @OA\Response(
     *     response=204,
     *     description="Contract updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Updated"
     * )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Contract not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="the contract with id 1 was not found"
     * )
     * )
     * @OA\RequestBody(
     *     request="Contract",
     *     description="Contract to update",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="type",
     *     type="string",
     *     example="CDI"
     *   )
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the contract",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $contract = $this->contractRepository->read($id);

        if (!$contract) {
           throw new ResourceNotFoundException(
               json_encode('the contract with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
               404
           );
        }

        $this->requestValidatorService->throwError400FromData($data, $contract);

        try {
            $contract->setType($request->get('type'));
            $this->contractRepository->update($contract);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse(['response' => 'contract updated'], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="Contract deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Deleted"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Contract not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="the contract with id 1 was not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the contract",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        if (!$this->contractRepository->read($id)) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The candidate with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $this->contractRepository->delete($id);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse('Deleted', 200);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException|\JsonException
     * @OA\Response(
     *     response=200,
     *     description="Contract list",
     *     @OA\JsonContent(
     *     type="string",
     *     example="[
     *     {
     *     ""id"": 1,
     *     ""type"": ""CDI""
     *    },
     *     {
     *     ""id"": 2,
     *     ""type"": ""CDD""
     *   }
     *     ]"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="No contract found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="No contract found"
     * )
     * )
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $contract = $this->contractRepository->list();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        if (!$contract) {
            throw new ResourceNotFoundException(json_encode('Contracts not found in database', JSON_THROW_ON_ERROR),
                404);
        }
        return new JsonResponse($this->serializer->serialize($contract, 'json'), 200, [], true);
    }
}