<?php

declare(strict_types=1);

namespace Paysera\BearerAuthenticationBundle\Tests;

use Paysera\BearerAuthenticationBundle\Security\BearerPassportAuthenticator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class BearerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (Kernel::MAJOR_VERSION < 5) {
            $this->markTestSkipped('Skipping tests for below symfony 5');
        }

        $this->authenticationManager = $this->createMock(AuthenticationManagerInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->userProvider = $this->createMock(UserProviderInterface::class);
    }

    /**
     * @dataProvider dataBearerHeaders
     */
    public function testBearerAuthenticatorSupport(string $bearerToken, bool $expected): void
    {
        $request = new Request();
        $request->headers->add(['Authorization' => $bearerToken]);

        $this->assertSame($expected, $this->createAuthenticator()->supports($request));
    }

    public function testOnAuthenticationSuccess(): void
    {
        $token = $this->createMock(TokenInterface::class);

        $this->tokenStorage
            ->expects($this->once())
            ->method('setToken')
            ->with($token)
        ;

        $response = $this
            ->createAuthenticator()
            ->onAuthenticationSuccess(new Request(), $token, 'test')
        ;

        $this->assertNull($response);
    }

    public function testAuthenticationFailedWhenBearerNotFound(): void
    {
        $request = new Request();
        $this->expectException(AuthenticationException::class);

        $this->createAuthenticator()->authenticate($request);
    }

    public function dataBearerHeaders(): array
    {
        return [
            ['Bearer abc123', true],
            ['Bearerabc123', false],
            ['Bearer123', false],
            ['Bearer@#$', false],
            ['Bear', false],
            ['', false],
        ];
    }

    private function createAuthenticator(): BearerPassportAuthenticator
    {
        return new BearerPassportAuthenticator(
            $this->tokenStorage,
            $this->logger,
            $this->userProvider,
        );
    }
}
