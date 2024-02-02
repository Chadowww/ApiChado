<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Services\RequestValidator\RequestEntityValidators\ContractRequestValidator;
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\ContractRepository;
use App\Services\ErrorService;
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
    private ContractRequestValidator $contractRequestValidator;
    private ContractRepository $contractRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ContractRequestValidator $contractRequestValidator,
        ContractRepository $contractRepository,
        SerializerInterface $serializer,
    )
    {
        $this->contractRequestValidator = $contractRequestValidator;
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
        $this->contractRequestValidator->getErrorsContractRequest($request);

        $contract = new Contract();
        $contract->setType($request->get('type'));

        try {
            $this->contractRepository->create($contract);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return new JsonResponse('Created', 201);
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
        try {
            $contract = $this->contractRepository->read($id);
            if (!$contract) {
                throw new resourceNotFoundException(
                    json_encode('the contract with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
                    404
                );
            }
        } catch (PDOException $e) {
            throw new databaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), $e->getCode());
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
        $this->contractRequestValidator->getErrorsContractRequest($request);

       $contract = $this->contractRepository->read($id);

       if (!$contract) {
           throw new ResourceNotFoundException(
               json_encode('the contract with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
               404
           );
       }

        try {
            $contract->setType($request->get('type'));
            $this->contractRepository->update($contract);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

       return new JsonResponse('Updated', 204);
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
        $contract = $this->contractRepository->read($id);

        if(!$contract) {
            throw new ResourceNotFoundException(
                json_encode('the contract with id ' . $id . ' was not found', JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $this->contractRepository->delete($contract);
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

        if (empty($contract)) {
            throw new ResourceNotFoundException(json_encode('No contract found', JSON_THROW_ON_ERROR), 404);
        }
        return new JsonResponse($this->serializer->serialize($contract, 'json'), 200, [], true);
    }
}