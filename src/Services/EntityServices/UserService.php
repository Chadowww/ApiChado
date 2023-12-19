<?php

namespace App\Services\EntityServices;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildUser(Request $request): User
    {
        if ($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            throw new InvalidArgumentException('User with this email already exists');
        }
        $user = new User();
        $user->setEmail($request->get('email'));
        $user->setPassword(password_hash($request->get('password'), PASSWORD_ARGON2ID));
        $user->setRoles($request->get('roles'));
        $user->setCreatedAt(new DateTime());
        $user->setUpdatedAt(new DateTime());
        return $user;
    }
}