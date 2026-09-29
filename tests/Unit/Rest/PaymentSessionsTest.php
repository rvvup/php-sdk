<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\Rest;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use Rvvup\Api\Model\PaymentMethodTokenDto;
use Rvvup\Api\PaymentSessionsApi;
use Rvvup\ApiException;
use Rvvup\Configuration;
use Rvvup\Sdk\Rest\PaymentSessions;
use Rvvup\Sdk\Rest\RvvupClient;

/**
 * @covers \Rvvup\Sdk\Rest\PaymentSessions::getSavedToken
 */
class PaymentSessionsTest extends TestCase
{
    private const MERCHANT_ID = 'merchant-test-1';
    private const SESSION_ID  = 'ps-abc123';

    private function makePaymentSessions(): array
    {
        $client = $this->createMock(RvvupClient::class);
        $client->method('getMerchantId')->willReturn(self::MERCHANT_ID);
        $client->method('configuration')->willReturn(new Configuration());

        $api = $this->createMock(PaymentSessionsApi::class);

        $service = new PaymentSessions($client);

        $prop = new ReflectionProperty(PaymentSessions::class, 'api');
        $prop->setAccessible(true);
        $prop->setValue($service, $api);

        return [$service, $api];
    }

    public function testGetSavedTokenReturnsTokenOnSuccess(): void
    {
        [$service, $api] = $this->makePaymentSessions();

        $token = new PaymentMethodTokenDto(['id' => 'tok-1', 'cardLast4' => '4242']);

        $api->expects($this->once())
            ->method('getSavedToken')
            ->with(self::MERCHANT_ID, self::SESSION_ID)
            ->willReturn($token);

        $result = $service->getSavedToken(self::SESSION_ID);

        $this->assertSame($token, $result);
    }

    public function testGetSavedTokenReturnsNullOn404(): void
    {
        [$service, $api] = $this->makePaymentSessions();

        $exception = new ApiException('Not Found', 404);

        $api->expects($this->once())
            ->method('getSavedToken')
            ->willThrowException($exception);

        $result = $service->getSavedToken(self::SESSION_ID);

        $this->assertNull($result);
    }

    public function testGetSavedTokenRethrowsOnNon404Error(): void
    {
        [$service, $api] = $this->makePaymentSessions();

        $exception = new ApiException('Internal Server Error', 500);

        $api->method('getSavedToken')->willThrowException($exception);

        $this->expectException(ApiException::class);
        $this->expectExceptionCode(500);

        $service->getSavedToken(self::SESSION_ID);
    }

    public function testGetSavedTokenSendsCorrectMerchantId(): void
    {
        [$service, $api] = $this->makePaymentSessions();

        $api->expects($this->once())
            ->method('getSavedToken')
            ->with(self::MERCHANT_ID, $this->anything())
            ->willReturn(new PaymentMethodTokenDto());

        $service->getSavedToken(self::SESSION_ID);
    }
}
