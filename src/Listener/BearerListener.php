<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Paysera\BearerAuthenticationBundle\Security\Token\BearerToken;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @deprecated since Symfony 5.4
 */
class BearerListener implements ListenerInterface
{
    private $tokenStorage;
    private $authenticationManager;
    private $bearerRegex;
    private $logger;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     * @param string $regex
     * @param LoggerInterface $logger
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        AuthenticationManagerInterface $authenticationManager,
        $regex,
        LoggerInterface $logger
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
        $this->bearerRegex = $regex;
        $this->logger = $logger;
    }

    public function handle(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $this->fixAuthHeader($request->headers);
        $this->logger->debug('authorization approach logged', $request->headers->all());

        if (
            !$request->headers->has('Authorization')
            || preg_match($this->bearerRegex, $request->headers->get('Authorization'), $matches) !== 1
        ) {
            $this->logger->debug('no Authorization header found in request', $request->headers->all());
            return;
        }
        $this->logger->debug('Authorization header found', [$request->headers->get('Authorization')]);
        $token = new BearerToken();
        $token->setToken($matches[1]);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->tokenStorage->setToken($authToken);
        } catch (AuthenticationException $exception) {
            $this->logger->debug('authentication failed for token', [$token]);
        }
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