<?php

namespace App\Services\EntityServices;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param Request $request
     * @return User
     * @throws \JsonException
     */
    public function buildUser(Request $request): User
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($this->userRepository->findOneBy(['email' => $request->get('email')])) {
            throw new InvalidArgumentException('User with this email already exists');
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles($data['roles']);
        $user->setUpdatedAt(new DateTime());

        return $user;
    }

    /**
     * @param Request $request
     * @param User $user
     * @return User
     * @throws \JsonException
     */
    public function UpdateUser(Request $request, User $user): User
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($this->userRepository->findOneBy(['email' => $data['email']]) && $user->getEmail() !== $data['email']) {
            throw new InvalidArgumentException('User with this email already exists');
        }

        $user->setEmail($data['email']);
        $user->setRoles($data['roles']);
        $user->setUpdatedAt(new DateTime());

        return $user;
    }
}