<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class UserAuthenticator extends AbstractAuthenticator
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        protected  readonly SerializerInterface $serializer
    )
    {}

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    /**
     * @throws \JsonException
     */
    public function authenticate(Request $request): Passport
    {
        $credentials = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        if ($credentials['email'] === null || $credentials['password'] === null) {
            throw new AuthenticationException('Email or password incorrect');
        }

        return new Passport(
            new UserBadge(
                $credentials['email'],
                function ($email) {
                    $user = $this->userRepository->findOneBy(['email' => $email]);

                    if (!$user) {
                        throw new UserNotFoundException();
                    }

                    if (!$user->isIsVerified()) {
                        throw new AuthenticationException('Your account is not verified. Please check your emails.');
                    }

                    return $user;
                }
            ),
            new PasswordCredentials($credentials['password'])
        );
    }

    /**
     * @throws \JsonException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        $user = $token->getUser();

        if ($user) {
            $jwtToken = $this->jwtTokenManager->create($user);
            return new JsonResponse(['token' => $jwtToken]);
        }
        return new JsonResponse(['token' => 'success']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => $exception->getMessageKey()
        ], Response::HTTP_UNAUTHORIZED);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
    public function loadUserByIdentifier(string $identifier): ?User
    {
        return $this->userRepository->findOneBy(['email' => $identifier]);
    }
}
