<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class ApiLoginAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
    )
    {

    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/api/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (!$username || !$password) {
            throw new CustomUserMessageAuthenticationException('Données d’identification manquantes');
        }

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        /** @var UserInterface $user */
        $user = $token->getUser();

        if (!in_array('ROLE_API_USER', $user->getRoles(), true)) {
            return new JsonResponse([
                'status' => 403,
                'message' => 'Accès API non activé',
            ], Response::HTTP_FORBIDDEN);
        }

        $jwt = $this->jwtManager->create($user);

        return new JsonResponse([
            'token' => $jwt,
        ]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'status' => 401,
            'message' => 'Identifiants incorrects',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
