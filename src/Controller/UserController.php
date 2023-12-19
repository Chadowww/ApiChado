<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\UserRepository;
use App\Services\EntityServices\UserService;
use App\Services\ErrorService;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @OA\Tag(name="User")
 */
class UserController extends AbstractController
{
    private ErrorService $errorService;
    private UserService $UserService;
    private UserRepository $userRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ErrorService $errorService,
        UserService $UserService,
        UserRepository $userRepository,
        SerializerInterface $serializer,

    )
    {
        $this->errorService = $errorService;
        $this->UserService = $UserService;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     * @OA\Response(
     *     response=201,
     *     description="User created",
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
     *     request="User",
     *     description="User to create",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="email",
     *     type="string",
     *     example="
     *          {
     *         'email': chado@email.fr'
     *          }
     *     "
     *    ),
     *     @OA\Property(
     *     property="password",
     *     type="string",
     *     example="
     *         {
     *     'password': '
     *     }
     *     "
     *  ),
     *     @OA\Property(
     *     property="roles",
     *     type="integer",
     *     description="1 = ROLE_USER, 3 = ROLE_CANDIDATE, 5 = ROLE_COMPANY, 9 = ROLE_ADMIN",
     *     example=1,
     *     enum={1, 2, 4, 8},
     * )))
     */    public function create(Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsUserRequest($request) !== []) {
            throw new InvalidRequestException(json_encode($this->errorService->getErrorsUserRequest($request), JSON_THROW_ON_ERROR), 400);
        }
        $user = $this->UserService->buildUser($request);

        try {
            $this->userRepository->create($user);
        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }
        
        return new JsonResponse(['201' => 'new user created'], 201);
    }

    /**
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="User found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="User found"
     *  )
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="User not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the user to read",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     *    )
     * )
     */
    public function read(int $id): JsonResponse
    {
        try {
            $user = $this->userRepository->read($id);
            if (!$user) {
                throw new resourceNotFoundException(
                    json_encode([
                        'error' => 'The user with id ' . $id . ' does not exist.'
                    ],
                    JSON_THROW_ON_ERROR),
                    404
                );
            }
        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }
        return new JsonResponse($this->serializer->serialize($user, 'json'), 200, [], true);
    }

    /**
     * @throws InvalidRequestException
     * @throws \JsonException
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @OA\Response(
     *     response=204,
     *     description="User updated",
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
     *     description="User not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="User not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the user to update",
     *     required=true,
     *     @OA\Schema(
     *     type="integer",
     *     example="1"
     *   )
     * )
     * @OA\RequestBody(
     *     request="User",
     *     description="User to update",
     *     required=true,
     *     @OA\JsonContent(
     *     type="object",
     *     @OA\Property(
     *     property="email",
     *     type="string",
     *     example="
     *         {
     *     'email': '
     *     }
     *     "
     *   ),
     *     @OA\Property(
     *     property="password",
     *     type="string",
     *     example="
     *        {
     *     'password': '
     *     }
     *     "
     *  ),
     *     @OA\Property(
     *     property="roles",
     *     type="integer",
     *     description="1 = ROLE_USER, 3 = ROLE_CANDIDATE, 5 = ROLE_COMPANY, 9 = ROLE_ADMIN",
     *     example=1,
     *     enum={1, 2, 4, 8},
     * )))
     */
    public function update(int $id, Request $request): JsonResponse
    {
        if ($this->errorService->getErrorsUserRequest($request) !== []) {
            throw new InvalidRequestException(json_encode($this->errorService->getErrorsUserRequest($request), JSON_THROW_ON_ERROR), 400);
        }
        $user = $this->userRepository->read($id);
        if (!$user) {
            throw new resourceNotFoundException(
                json_encode([
                    'error' => 'The user with id ' . $id . ' does not exist.'
                ],
                    JSON_THROW_ON_ERROR),
                404
            );
        }

        try {
            $user = $this->UserService->UpdateUser($request, $user);
            $this->userRepository->update($user);
        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }

        return new JsonResponse('Updated', 204);
    }

    /**
     * @throws ResourceNotFoundException
     * @throws DatabaseException
     * @throws \JsonException
     * @OA\Response(
     *     response=204,
     *     description="User deleted",
     *     @OA\JsonContent(
     *     type="string",
     *     example="Deleted"
     * )
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="User not found"
     * )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Id of the user to delete",
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
            $user = $this->userRepository->read($id);
            if (!$user) {
                throw new resourceNotFoundException(
                    json_encode([
                        'error' => 'The user with id ' . $id . ' does not exist.'
                    ],
                        JSON_THROW_ON_ERROR),
                    404
                );
            }
            $this->userRepository->delete($id);
        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }

        return new JsonResponse('User has been deleted', 204, [], false);
    }

    /**
     * @throws DatabaseException
     */
    public function list(): JsonResponse
    {
        try {
            $users = $this->userRepository->list();
        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }

        return new JsonResponse($this->serializer->serialize($users, 'json'), 200, [], true);
    }
}
