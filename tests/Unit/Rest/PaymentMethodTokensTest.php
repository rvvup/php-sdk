<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\Rest;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Rvvup\Api\PaymentMethodTokensApi;
use Rvvup\ApiException;
use Rvvup\Configuration;
use Rvvup\Sdk\Rest\PaymentMethodTokens;
use Rvvup\Sdk\Rest\RvvupClient;

/**
 * @covers \Rvvup\Sdk\Rest\PaymentMethodTokens::revoke
 */
class PaymentMethodTokensTest extends TestCase
{
    private const MERCHANT_ID = 'merchant-test-1';
    private const TOKEN_ID    = 'tok-abc123';

    private function makePaymentMethodTokens(): array
    {
        $client = $this->createMock(RvvupClient::class);
        $client->method('getMerchantId')->willReturn(self::MERCHANT_ID);
        $client->method('configuration')->willReturn(new Configuration());

        $api = $this->createMock(PaymentMethodTokensApi::class);

        $service = new PaymentMethodTokens($client);

        $prop = new ReflectionProperty(PaymentMethodTokens::class, 'api');
        $prop->setAccessible(true);
        $prop->setValue($service, $api);

        return [$service, $api];
    }

    public function testRevokeReturnsTrueOnSuccess(): void
    {
        [$service, $api] = $this->makePaymentMethodTokens();

        $api->expects($this->once())
            ->method('revokePaymentMethodToken')
            ->with(self::MERCHANT_ID, self::TOKEN_ID);

        $result = $service->revoke(self::TOKEN_ID);

        $this->assertTrue($result);
    }

    public function testRevokeReturnsFalseOn404(): void
    {
        [$service, $api] = $this->makePaymentMethodTokens();

        $exception = new ApiException('Not Found', 404);

        $api->expects($this->once())
            ->method('revokePaymentMethodToken')
            ->willThrowException($exception);

        $result = $service->revoke(self::TOKEN_ID);

        $this->assertFalse($result);
    }

    public function testRevokeRethrowsOnNon404Error(): void
    {
        [$service, $api] = $this->makePaymentMethodTokens();

        $exception = new ApiException('Forbidden', 403);

        $api->method('revokePaymentMethodToken')->willThrowException($exception);

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(403);

        $service->revoke(self::TOKEN_ID);
    }

    public function testRevokeSendsCorrectMerchantId(): void
    {
        [$service, $api] = $this->makePaymentMethodTokens();

        $api->expects($this->once())
            ->method('revokePaymentMethodToken')
            ->with(self::MERCHANT_ID, $this->anything());

        $service->revoke(self::TOKEN_ID);
    }
}
