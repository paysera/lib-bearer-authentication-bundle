<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Security;

use Paysera\BearerAuthenticationBundle\Security\Token\BearerToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class BearerPassportAuthenticator implements AuthenticatorInterface
{
    public const BEARER_REGEX = '/Bearer\s+(\S+)/';

    private AuthenticationManagerInterface $authenticationManager;
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private UserProviderInterface $userProvider;

    public function __construct(
        AuthenticationManagerInterface $authenticationManager,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger,
        UserProviderInterface $userProvider
    ) {
        $this->authenticationManager = $authenticationManager;
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
            $user = $this->userProvider->loadUserByIdentifier($token->getToken());
            $authToken = $this->authenticationManager->authenticate($token);

            $this->tokenStorage->setToken($authToken);
            $token->setAttributes($token->getAttributes());
            $token->setUser($user);

            $this->logger->info('Authenticated', [$authToken->getUser()]);

            $passport = new SelfValidatingPassport(new UserBadge($token->getToken()));
            $passport->setAttribute('bearer', $token);

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
        return new Response('Invalid bearer token', Response::HTTP_UNAUTHORIZED);
    }

    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        trigger_deprecation(
            'symfony/security-http',
            '5.4',
            'Method "%s()" is deprecated, use "%s::createToken()" instead.',
            __METHOD__,
            __CLASS__
        );

        return $this->createToken($passport, $firewallName);
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
