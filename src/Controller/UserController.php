<?php

namespace App\Controller;

use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Repository\UserRepository;
use App\Services\EntityServices\UserService;
use App\Services\ErrorService;
use PDOException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    private ErrorService $errorService;
    private UserService $UserService;
    private UserRepository $userRepository;

    public function __construct(ErrorService $errorService, UserService $UserService, UserRepository $userRepository)
    {
        $this->errorService = $errorService;
        $this->UserService = $UserService;
        $this->userRepository = $userRepository;
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
     *     'password': Fw7jzpdr7!'
     *     }
     *     "
     *  ),
     *     @OA\Property(
     *     property="roles",
     *     type="integer",
     *     example="
     *        {
     *     'roles': 3'
     *     }
     *     "
     * )))
     */
    public function create(Request $request): JsonResponse
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

    public function read()
    {

    }

    public function update()
    {

    }

    public function delete()
    {

    }

    public function list()
    {

    }
}