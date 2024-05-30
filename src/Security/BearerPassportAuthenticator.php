<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security;

use Paysera\BearerAuthenticationBundle\Security\Token\BearerToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class BearerPassportAuthenticator extends AbstractAuthenticator
{
    public const BEARER_REGEX = '/Bearer\s+(\S+)/';

    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private UserProviderInterface $userProvider;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
        UserProviderInterface $userProvider
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        $this->logger->debug('bearer authorization approach logged', $request->headers->all());
        $this->fixAuthHeader($request->headers);

        return $request->headers->has('Authorization')
            && $this->getBearerToken($request->headers) !== null
        ;
    }

    private function getBearerToken(HeaderBag $headers): ?string
    {
        if ($headers->get('Authorization') === null) {
            return null;
        }

        $matches = [];
        $result = preg_match(self::BEARER_REGEX, $headers->get('Authorization'), $matches);

        if ($result !== 1) {
            return null;
        }

        return $matches[1] ?? null;
    }

    public function authenticate(Request $request): Passport
    {
        $bearerToken = $this->getBearerToken($request->headers);
        if ($bearerToken === null) {
            $this->logger->debug('no Authorization header found in request', $request->headers->all());
            throw new AuthenticationException('Token not found');
        }

        $token = new BearerToken();
        $token->setToken($bearerToken);

        try {
            $this->tokenStorage->setToken($token);

            $passport = new SelfValidatingPassport(new UserBadge($token->getToken(), [$this->userProvider, 'loadUserByIdentifier']));

            $this->logger->info('Authenticated', [$passport->getUser()->getUsername()]);
            $passport->setAttribute('bearer', $token);
            $token->setUser($passport->getUser());
            $token->setAuthenticated(true);
            $token->setAttributes($token->getAttributes());

            return $passport;
        } catch (AuthenticationException $exception) {
            $this->logger->debug('authentication failed for token', [$token]);

            throw $exception;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $this->tokenStorage->setToken($token);

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $this->logger->warning('Bearer authentication failed', [
            $exception->getMessage(),
            $exception->getToken()
        ]);

        return new JsonResponse(['error' => 'unauthorized' ,'error_description' => 'No authorization data found'], Response::HTTP_UNAUTHORIZED);
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return $passport->getAttribute('bearer');
    }

    private function fixAuthHeader(HeaderBag $headers): void
    {
        if (function_exists('apache_request_headers') && !$headers->has('Authorization')) {
            $apacheHeaders = apache_request_headers();
            if (isset($apacheHeaders['Authorization'])) {
                $headers->set('Authorization', $apacheHeaders['Authorization']);
            }
        }
    }
}
