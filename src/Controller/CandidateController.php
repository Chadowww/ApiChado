<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\CandidateRepository;
use App\Services\ErrorService;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use App\Services\EntityServices\CandidateService;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Candidate")
 */
class CandidateController extends AbstractController
{

    private ErrorService $errorService;
    private CandidateService $candidateService;
    private CandidateRepository $candidateRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ErrorService $errorService,
        CandidateService $candidateService,
        CandidateRepository $candidateRepository,
        SerializerInterface $serializer,
    )
    {
        $this->errorService = $errorService;
        $this->candidateService = $candidateService;
        $this->candidateRepository = $candidateRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     * @OA\Response(
     *     response=201,
     *     description="Candidate created",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Created"
     *  )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Invalid request",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Invalid request"
     * )
     * )
     * @OA\RequestBody(
     *     request="Candidate",
     *     description="Candidate object that needs to be added to the database",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="firstName",
     *     type="string",
     *     example="John"
     *    ),
     *     @OA\Property(
     *     property="lastName",
     *     type="string",
     *     example="Doe"
     *   ),
     *     @OA\Property(
     *     property="phone",
     *     type="string",
     *     example="
     *       {
     *     'phone': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="address",
     *     type="string",
     *     example="
     *      {
     *     'address': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="city",
     *     type="string",
     *     example="
     *     {
     *     'city': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="country",
     *     type="string",
     *     example="
     *   {
     *     'country': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="user_id",
     *     type="integer",
     *     example="
     *    {
     *     'user_id': '
     *     }
     *     "
     * )))
     */
    public function create(Request $request): JsonResponse
    {
        if($this->errorService->getErrorsCandidateRequest($request) !== []){
            throw new InvalidRequestException(json_encode($this->errorService->getErrorsCandidateRequest($request),
                JSON_THROW_ON_ERROR), 400);
        }
        $candidate = $this->candidateService->createCandidate($request);

        try {
            $this->candidateRepository->create($candidate);
        } catch (PDOException $exception) {
            throw new InvalidRequestException($exception->getMessage(), 400);
        }

        return new JsonResponse(['message' => 'Candidate created'], 201);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="Candidate found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Candidate found"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Candidate not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Candidate not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the candidate to read",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     *   )
     * )
     */
    public function read(int $id): JsonResponse
    {
        try {
            $candidate = $this->candidateRepository->read($id);
            if (!$candidate) {
                throw new resourceNotFoundException(
                    json_encode([
                        'error' => 'The candidate with id ' . $id . ' does not exist.'
                    ],
                        JSON_THROW_ON_ERROR),
                    404
                );
            }
        } catch (PDOException $exception) {
            throw new DatabaseException($exception->getMessage(), 500);

        }
        return new JsonResponse($this->serializer->serialize($candidate, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @OA\Response(
     *     response=200,
     *     description="Candidate updated",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Candidate updated"
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
     *     description="Candidate not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Candidate not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the candidate to update",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     *  )
     * )
     * @OA\RequestBody(
     *     request="Candidate",
     *     description="Candidate object that needs to be updated in the database",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="firstName",
     *     type="string",
     *     example="John"
     *   ),
     *     @OA\Property(
     *     property="lastName",
     *     type="string",
     *     example="Doe"
     *  ),
     *     @OA\Property(
     *     property="phone",
     *     type="string",
     *     example="
     *      {
     *     'phone': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="address",
     *     type="string",
     *     example="
     *     {
     *     'address': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="city",
     *     type="string",
     *     example="
     *    {
     *     'city': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="country",
     *     type="string",
     *     example="
     *    {
     *     'country': '
     *     }
     *     "
     * ),
     *     @OA\Property(
     *     property="user_id",
     *     type="integer",
     *     example="
     *    {
     *     'user_id': '
     *     }
     *     "
     * )))
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if($this->errorService->getErrorsCandidateRequest($request) !== []){
            throw new InvalidRequestException(json_encode($this->errorService->getErrorsCandidateRequest($request),
                JSON_THROW_ON_ERROR), 400);
        }

        $candidate = $this->candidateRepository->read($id);

        if (!$candidate){
            throw new resourceNotFoundException(
                json_encode([
                    'error' => 'The candidate with id ' . $id . ' does not exist.'
                ],
                    JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $this->candidateService->updateCandidate($candidate, $request);
            $this->candidateRepository->update($candidate);
        } catch (PDOException $exception) {
            throw new DatabaseException($exception->getMessage(), 500);
        }

        return new JsonResponse(['message' => 'Candidate updated'], 200);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="Candidate deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Candidate deleted"
     *  )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Candidate not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Candidate not found"
     *  )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the candidate to delete",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     *  )
     * )
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $candidate = $this->candidateRepository->read($id);
            if (!$candidate) {
                throw new resourceNotFoundException(
                    json_encode([
                        'error' => 'The candidate with id ' . $id . ' does not exist.'
                    ],
                        JSON_THROW_ON_ERROR),
                    404
                );
            }
            $this->candidateRepository->delete($id);

            return new JsonResponse(['message' => 'Candidate deleted'], 200);

        } catch (PDOException $exception) {
            throw new DatabaseException($exception->getMessage(), 500);
        }

    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="List of candidates",
     *     @OA\JsonContent(
     *     type="string",
     *     example="List of candidates"
     * )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Database error",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Database error"
     * )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     required=false,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     * )
     * )
     */
    public function list(): JsonResponse
    {
        try {
            $candidates = $this->candidateRepository->list();
        } catch (PDOException $exception) {
            throw new DatabaseException($exception->getMessage(), 500);
        }

        return new JsonResponse($this->serializer->serialize($candidates, 'json'), 200, [], true);
    }
}