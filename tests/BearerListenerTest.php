<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Tests;

use Paysera\BearerAuthenticationBundle\Listener\BearerListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Tests\KernelTest;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class BearerListenerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (Kernel::MAJOR_VERSION !== 4) {
            $this->markTestSkipped(sprintf('Skipping %s tests for symfony:%d', __CLASS__, Kernel::MAJOR_VERSION));
        }

        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->authenticationManager = $this->createMock(AuthenticationManagerInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->kernel = $this->createMock(HttpKernelInterface::class);
        $this->listener = new BearerListener($this->tokenStorage, $this->authenticationManager, '/Bearer\s+(\S+)/', $this->logger);
    }

    public function testAuthenticationSuccess(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_Authorization' => 'Bearer token123']);
        $event = new KernelEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $authToken = $this->createMock(TokenInterface::class);

        $this->authenticationManager->expects($this->once())
            ->method('authenticate')
            ->willReturn($authToken)
        ;

        $this->tokenStorage->expects($this->once())
            ->method('setToken')
            ->with($authToken)
        ;

        $this->listener->handle($event);
    }

    public function testAuthenticationError(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_Authorization' => 'Bearer INVALID TOKEN']);
        $event = new KernelEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $this->authenticationManager->expects($this->once())
            ->method('authenticate')
            ->willThrowException(new AuthenticationException())
        ;

        $this->listener->handle($event);
    }
}
