<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\CandidateRepository;
use App\Repository\UserRepository;
use App\Services\EntityServices\UserService;
use App\Services\ErrorService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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
     */
    public function create(Request $request): JsonResponse
    {
        if ($this->userRepository->findByEmail($request)) {
            throw new InvalidRequestException(
                json_encode(['error' => 'This email is already used'], JSON_THROW_ON_ERROR),
                400
            );
        }
        if ($this->errorService->getErrorsUserRequest($request) !== []) {
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsUserRequest($request), JSON_THROW_ON_ERROR),
                400
            );
        }
        $user = $this->UserService->buildUser($request);
        try {
            $this->userRepository->create($user);
        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }
        $lastId = $this->userRepository->getLastId();
        return new JsonResponse([
            '201' => 'new user created',
            'userId' => $lastId,
            ],
        201
        );
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
     *     response=200,
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
            throw new InvalidRequestException(
                json_encode($this->errorService->getErrorsUserRequest($request), JSON_THROW_ON_ERROR),
                400
            );
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

        return new JsonResponse(['200' => 'user updated'], 200);
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

            return new JsonResponse('User has been deleted', 204, [], false);

        } catch (PDOException $e) {
            throw new DatabaseException($this->json(['error' => $e->getMessage()]), 500);
        }
    }

    /**
     * @throws DatabaseException
     * @throws \JsonException
     * @OA\Response(
     *     response=200,
     *     description="List of users",
     *     @OA\JsonContent(
     *     type="string",
     *     example="List of users"
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

    /**
     * @throws DatabaseException
     * @throws \JsonException
     * /**
     * @OA\Response(
     *      response=200,
     *      description="User found",
     *      @OA\JsonContent(
     *          type="object",
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="email", type="string", example="e.email@mail.de"),
     *          @OA\Property(property="password", type="string", example="$argon2id$v=19$m=10,t=2,p=1$Rnc3anpwZHI3$bIwLu12m1AEf2ANUZQJVow"),
     *          @OA\Property(property="roles", type="integer", example=3),
     *          @OA\Property(property="is_verified", type="integer", example=1),
     *          @OA\Property(property="created_at", type="string", example="2023-12-31 12:40:28"),
     *          @OA\Property(property="updated_at", type="string", example="2024-01-03 12:40:28"),
     *          @OA\Property(property="firstname", type="string", example="Edward"),
     *          @OA\Property(property="lastname", type="string", example="Email"),
     *          @OA\Property(property="phone", type="string", example="1234567890"),
     *          @OA\Property(property="address", type="string", example="10 rue de la paix"),
     *          @OA\Property(property="city", type="string", example="Paris"),
     *          @OA\Property(property="country", type="string", example="France"),
     *          @OA\Property(property="avatar", type="string", example="659e5ecc4d5217.08563480.jpg"),
     *          @OA\Property(property="slug", type="string", example="edward-email"),
     *          @OA\Property(property="coverLetter", type="string", example=null),
     *          @OA\Property(property="userId", type="integer", example=18),
     *          @OA\Property(property="linkedin", type="string", example="https://www.linkedin.com/in/edward-email/"),
     *          @OA\Property(property="github", type="string", example="https://github.com/edward-email"),
     *          @OA\Property(property="twitter", type="string", example=null),
     *          @OA\Property(property="facebook", type="string", example=null),
     *          @OA\Property(property="instagram", type="string", example=null),
     *          @OA\Property(property="website", type="string", example="www.ee.turing.fr"),
     *      )
     *  )
     * /
     * @OA\Response(
     *     response=404,
     *     description="User not found",
     *     @OA\JsonContent(
     *     type="string",
     *     example="User not found"
     * )
     * )
     * @OA\Parameter(
     *     name="token",
     *     in="header",
     *     description="Token of the user to read",
     *     required=true,
     *     @OA\Schema(
     *     type="string",
     *     example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MDQ4Nzc3NTUsImV4cCI6MTcwNDg4MTM1NSwicm9sZXMiOlsiUk9MRV9VU0VSIiwiUk9MRV9DQU5ESURBVEUiXSwidXNlcm5hbWUiOiJhLnNhbGVAb3V0bG9vay5mciJ9.k3OX2Ctw7WyqLby8bPOC0texHRuwMj1zRiqyeJd_YlT2s3vvM7NDYjTg3TfpjKDni6RpY3t7J1PkByAmXdzc_rgKTPPltrs_AQQsRTYPJeamGUrj7QKs4hlpM9ZhkbcQV1gSxnRkd49TQ9L3DOg_WVlv9axYXPiJgbvnlC7qSwLih5qLgPfWfYOnwWUj8T3KtQoXOLK5HGvM56moMX9loCG8QU1AbYwZaK2aS-3Y-Wdr4fNqAFaAyVfdJfGBWiFi7fuXAcPiOTzAMHGeMXv5vWOOxJgKND3h-tDcynkTj7iSvHAQP_vJk8enOgQlGHm7hBoAnh2p-qkNlZfndi6R2Q"
     * )
     * )
     */
    public function getUserFromToken(TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();
        $dataUser = [];
        if ($user) {
            $dataUser['user'] = $this->userRepository->getUserWithCandidate($user->getUserId());

            return new JsonResponse(
                $this->serializer->serialize($dataUser['user'], 'json'),
                200,
                [],
                true
            );
        }
        return new JsonResponse(
            json_encode(['error' => 'User not found'], JSON_THROW_ON_ERROR),
            404
        );
    }
}
