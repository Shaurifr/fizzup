<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FizzUpAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'jwt_login';
    public const JWT_KEY = '365x4g6d5fg41ds65fdfgd35f4g98qfv13';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     *
     * Override to change the request conditions that have to be
     * matched in order to handle the login form submit.
     *
     * This default implementation handles all POST requests to the
     * login path (@see getLoginUrl()).
     */
    public function supports(Request $request): bool
    {
        dump('supports');
        if (
            $request->headers->get('authorization') &&
            mb_stripos($request->headers->get('authorization'), 'Bearer ') === 0) {
            return true;
        }
        if ($request->headers->get('Content-Type', '') !== 'application/json') {
            return false;
        }
        $content = json_decode($request->getContent());
        if (!$content) {
            return false;
        }
        return $request->isMethod('POST') &&
            $this->getLoginUrl($request) === $request->getPathInfo() &&
            $content->username !== null &&
            $content->password !== null;
    }

    public function authenticate(Request $request): Passport
    {
        dump('authenticate');
        if (
            $request->headers->get('authorization') &&
            mb_stripos($request->headers->get('authorization'), 'Bearer ') === 0) {
            $jwt = substr($request->headers->get('authorization'), 7);

            // valider le jwt
            try {
                $jwtDecoded = JWT::decode($jwt, new Key(self::JWT_KEY, 'HS256'));
            } catch (\Exception $exception) {
                throw new UnauthorizedHttpException('Invalid token');
            }

            // si jwt valide, on renvoie le passport
            return new SelfValidatingPassport(new UserBadge($jwtDecoded->email), []);
        }
        $content = json_decode($request->getContent());
        $email = $content->username;
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($content->password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if (
            $request->headers->get('authorization') &&
            mb_stripos($request->headers->get('authorization'), 'Bearer ') === 0) {

            return null;
        }

        dump('success');
        // génération d'un token jwt
        $payload = [
            'email' => $token->getUser()->getUserIdentifier(),
        ];
        $jwt = JWT::encode($payload, self::JWT_KEY, 'HS256');

        return new JsonResponse(['token' => $jwt]);
    }

    /**
     * Override to change what happens after a bad username/password is submitted.
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        dump('failure');
        return new Response('Invalid Credentials', Response::HTTP_UNAUTHORIZED);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
