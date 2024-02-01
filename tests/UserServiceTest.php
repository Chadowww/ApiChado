<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\EntityServices\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    /**
     * @return void
     * @throws \JsonException
     */
    public function testBuildUser(): void
    {
        $mockUserRepository = $this->createMock(UserRepository::class);
        $mockUserPasswordHasherInterface = $this->createMock(UserPasswordHasherInterface::class);
        $data = [
            'email' => 'email@email.fr',
            'password' => 'Hd8kzpyr5!',
            'roles' => 3
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $userService = new UserService($mockUserRepository, $mockUserPasswordHasherInterface);
        $user = $userService->buildUser($request);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->getEmail(), $data['email']);
        $this->assertEquals($user->getRolesValue(), $data['roles']);
        $this->assertNotNull($user->getPassword());
    }

    /**
     * @return void
     * @throws \JsonException
     */
    public function testUpdateUser(): void
    {
        $mockUserRepository = $this->createMock(UserRepository::class);
        $mockUserPasswordHasherInterface = $this->createMock(UserPasswordHasherInterface::class);
        $user = new User();
        $user->setEmail('email@mail.fr');
        $user->setRoles(3);
        $user->setPassword('Hd8kzpyr5!');

        $data = [
            'email' => 'newmail@email.fr',
            'roles' => 5
        ];

        $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

        $userService = new UserService($mockUserRepository, $mockUserPasswordHasherInterface);
        $user = $userService->updateUser($request, $user);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->getEmail(), $data['email']);
        $this->assertEquals($user->getRolesValue(), $data['roles']);
    }
}
