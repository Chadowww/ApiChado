<?php

namespace App\Tests;

use App\Controller\UserController;
use App\Entity\User;
use App\Exceptions\DatabaseException;
use App\Exceptions\InvalidRequestException;
use App\Exceptions\ResourceNotFoundException;
use App\Repository\UserRepository;
use App\Services\EntityServices\EntityBuilder;
use App\Services\RequestValidator\RequestValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class UserTest extends TestCase
{
    CONST array USER_DATA = [
        'userId' => 1,
        'email' => 'email@email.fr',
        'password' => '$2y$13$H0NP8BaamgdzlIi7aEFPz.jiwMHN51/HqufKi4uB0tDJTRsL9eCkK',
    ];

    private RequestValidatorService $requestValidatorService;
    private EntityBuilder $entityBuilder;
    private UserRepository $userRepository;
    private UserController $userController;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->requestValidatorService = $this->createMock(RequestValidatorService::class);
        $this->entityBuilder = $this->createMock(EntityBuilder::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $serializer = $this->createMock(SerializerInterface::class);
        $this->userController = new UserController(
            $this->requestValidatorService,
            $this->entityBuilder,
            $this->userRepository,
            $serializer,
        );
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testUserCreate(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));
        $this->entityBuilder->method('buildEntity')->willReturn($this->createMock(User::class));
        $this->userRepository->method('create')->willReturn(true);

        $response = $this->userController->create($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testUserCreateError400(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(InvalidRequestException::class);
        $this->requestValidatorService->expects($this->once())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Invalid request', 400));

        $this->entityBuilder->method('buildEntity')->willReturn($this->createMock(User::class));
        $this->userRepository->method('create')->willReturn(false);

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $response = $this->userController->create($request);
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws \JsonException
     */
    public function testUserCreateError500(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));
        $request->setMethod('POST');
        $request->headers->set('Content-Type', 'application/json');

        $this->entityBuilder->method('buildEntity')->willReturn(new User(self::USER_DATA));
        $this->userRepository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new DatabaseException('message d\'erreur', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $response = $this->userController->create($request);
    }

    /**
     * @return void
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserRead(): void
    {
        $user = new User();

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($user);

        $response = $this->userController->read(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserReadError404(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $response = $this->userController->read(1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserReadError500(): void
    {
        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $response = $this->userController->read(1);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserUpdate(): void
    {
        $user = new User();
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($user);

        $this->entityBuilder
            ->expects($this->once())
            ->method('buildEntity')
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->willReturn(true);

        $response = $this->userController->update(1, $request);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserUpdateError400(): void
    {
        $user = new User();
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(InvalidRequestException::class);
        $this->expectExceptionCode(400);

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($user);

        $this->requestValidatorService
            ->expects($this->once())
            ->method('throwError400FromData')
            ->willThrowException(new InvalidRequestException('Bad request', 400));

        $response = $this->userController->update(1, $request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserUpdateError404(): void
    {
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $response = $this->userController->update(1, $request);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws InvalidRequestException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserUpdateError500(): void
    {
        $user = new User();
        $request = new Request([], [], [], [], [], [], json_encode(self::USER_DATA, JSON_THROW_ON_ERROR));
        $this->expectException(DatabaseException::class);
        $this->expectExceptionCode(500);

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($user);

        $this->entityBuilder
            ->expects($this->once())
            ->method('buildEntity')
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->userController->update(1, $request);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserDelete(): void
    {
        $user = new User();

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($user);

        $this->userRepository
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $response = $this->userController->delete(1);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserDeleteError404(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $response = $this->userController->delete(1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws DatabaseException
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserDeleteError500(): void
    {
        $user = new User();

        $this->userRepository
            ->expects($this->once())
            ->method('read')
            ->willReturn($user);

        $this->expectException(DatabaseException::class);
        $this->userRepository
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new DatabaseException('An execption was throw', 500));

        $response = $this->userController->delete(1);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserList(): void
    {
        $user = new User();

        $this->userRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn([$user]);

        $response = $this->userController->list();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserListError404(): void
    {
        $user = new User();

        $this->userRepository
            ->expects($this->once())
            ->method('list')
            ->willReturn([]);

        $this->expectException(ResourceNotFoundException::class);
        $response = $this->userController->list();
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @return void
     * @throws ResourceNotFoundException
     * @throws \JsonException
     */
    public function testUserList500(): void
    {
        $user = new User();

        $this->expectException(DatabaseException::class);
        $this->userRepository
            ->expects($this->once())
            ->method('list')
            ->willThrowException(new DatabaseException('An error was throw', 500));

        $response = $this->userController->list();
        $this->assertEquals(500, $response->getStatusCode());
    }
}
