<?php

namespace App\Controller;

use App\Entity\{Candidate, User};
use App\Exceptions\{DatabaseException, InvalidRequestException, ResourceNotFoundException};
use App\Repository\CandidateRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use JsonException;
use OpenApi\Annotations as OA;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request};
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="Candidate")
 */
class CandidateController extends AbstractController
{
    /**
     * @param RequestValidatorService $requestValidatorService
     * @param EntityBuilder $entityBuilder
     * @param CandidateRepository $candidateRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly RequestValidatorService $requestValidatorService,
        private readonly EntityBuilder           $entityBuilder,
        private readonly CandidateRepository     $candidateRepository,
        private readonly SerializerInterface     $serializer
    ) {}

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
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
     *     property="userId",
     *     type="integer",
     *     example="
     *    {
     *     'userId': '
     *     }
     *     "
     * )))
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $candidate = new Candidate();

        $this->requestValidatorService->throwError400FromData($data, $candidate);

        $candidate = $this->entityBuilder->buildEntity($candidate, $data);

        $this->candidateRepository->create($candidate);

        return new JsonResponse(['message' => 'Candidate created successfully'], 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     *
     * @throws ResourceNotFoundException|JsonException|DatabaseException
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
     * )
     * )
     */
    public function read(int $id): JsonResponse
    {
        $candidate = $this->candidateRepository->read($id);

        if (!$candidate) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The candidate with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($candidate, 'json'), 200, [], true);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws JsonException
     * @throws ResourceNotFoundException
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
     *     property="userId",
     *     type="integer",
     *     example="
     *    {
     *     'userId': '
     *     }
     *     "
     * )))
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $candidate = $this->candidateRepository->read($id);

        if (!$candidate) {
            throw new ResourceNotFoundException(
                json_encode(['error' => 'The candidate with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->requestValidatorService->throwError400FromData($data, $candidate);

        $candidate = $this->entityBuilder->buildEntity($candidate, $data);

        try {
            $this->candidateRepository->update($candidate);
        } catch (PDOException $e) {
            throw new DatabaseException(json_encode($e->getMessage(), JSON_THROW_ON_ERROR), 500);
        }

        return new JsonResponse(['message' => 'Candidate updated successfully'], 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws DatabaseException
     * @throws JsonException
     * @throws ResourceNotFoundException
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
       if (!$this->candidateRepository->read($id)) {
            throw new resourceNotFoundException(
                json_encode(['error' => 'The candidate with id ' . $id . ' does not exist.'], JSON_THROW_ON_ERROR),
                404
            );
        }

        $this->candidateRepository->delete($id);

        return new JsonResponse(['message' => 'Candidate deleted successfully'], 200);
    }

    /**
     * @throws DatabaseException|JsonException|ResourceNotFoundException
     * @return JsonResponse
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
        $candidates = $this->candidateRepository->list();

        if (!$candidates) {
            throw new ResourceNotFoundException(
                json_encode('Candidates not found in database', JSON_THROW_ON_ERROR),
                404
            );
        }

        return new JsonResponse($this->serializer->serialize($candidates, 'json'), 200, [], true);
    }

    /**
     * Uploads an avatar for the authenticated user's candidate profile.
     *
     * @param Request $request The HTTP request object containing the avatar image file.
     * @param ImageController $imageController The image controller for handling image uploads.
     *
     * @return JsonResponse Returns a JSON response indicating the success or failure of the avatar upload.
     *
     * @throws ResourceNotFoundException|DatabaseException|JsonException If the candidate for the authenticated user
     * does not exist.
     * @OA\Response(
     *     response=200,
     *     description="File uploaded with success!",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="201", type="string", example="File uploaded with success!"),
     *     @OA\Property(property="fileName", type="string", example="example.jpg")
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="Candidate not found",
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(property="message", type="string", example="file not uploaded! :s")
     * )
     * )
     */
    public function uploadAvatar(Request $request, ImageController $imageController): JsonResponse
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            $candidate = $this->candidateRepository->getByUserId($user->getUserId());

            if (!$candidate) {
                throw new resourceNotFoundException(
                    json_encode([
                        'error' => 'The candidate with id ' . $user->getUserId() . ' does not exist.'
                    ],
                        JSON_THROW_ON_ERROR),
                    404
                );
            }
            $upload = $imageController->create($request);
            $jsonDecode = json_decode($upload->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if ($jsonDecode->get['code'] === '201') {
                try {
                    $candidate->setAvatar($jsonDecode['name']);
                    $this->candidateRepository->update($candidate);

                    return new JsonResponse([
                        '201' => 'File uploaded with success!', 'fileName' =>
                        $jsonDecode['name']
                    ]);
                } catch (PDOException $exception) {
                    throw new DatabaseException($exception->getMessage(), 500);
                }
            }
        }
        return new JsonResponse(['message' => 'file not uploaded! :s']);
    }
}