<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\GraphQlSdk;

use Exception;
use Rvvup\Sdk\Curl;
use Rvvup\Sdk\Exceptions\NetworkException;
use Rvvup\Sdk\Response;
use Rvvup\Sdk\Tests\HelperTrait;

/**
 * @test
 * @group order
 * @group refund
 */
class GetOrderRefundsTest extends AbstractGraphQlSdkTestCase
{
    use HelperTrait;

    /**
     * @test
     * @group refund
     *
     * @return void
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function assert_successful_get_order_refunds_call(): void
    {
        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = $this->createGraphQlSdk($curlStub);

        $orderId = $this->getRandomString();

        $response = new Response(
            200,
            json_encode($this->getOrderRefundsResponseData($orderId), JSON_THROW_ON_ERROR), []
        );

        $curlStub->method('request')->willReturn($response);

        $this->assertIsArray($graphQlSdk->getOrderRefunds($orderId));
    }

    /**
     * @test
     * @group refund
     *
     * @return void
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function assert_successful_result_get_order_refunds_call(): void
    {
        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = $this->createGraphQlSdk($curlStub);

        $orderId = $this->getRandomString();

        $response = new Response(
            200,
            json_encode($this->getOrderRefundsResponseData($orderId), JSON_THROW_ON_ERROR), []
        );

        $curlStub->method('request')->willReturn($response);

        $responseData = $this->getOrderRefundsResponseData($orderId)['data']['order'];

        $this->assertSame($responseData, $graphQlSdk->getOrderRefunds($orderId));
    }

    /**
     * @test
     *
     * @return void
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function assert_false_on_empty_response(): void
    {
        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = $this->createGraphQlSdk($curlStub);

        $response = new Response(200, json_encode([], JSON_THROW_ON_ERROR), []);

        $curlStub->method('request')->willReturn($response);

        $this->assertFalse($graphQlSdk->getOrderRefunds($this->getRandomString()));
    }

    /**
     * @test
     *
     * @return void
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function assert_exception_on_non_2xx_response_code(): void
    {
        $this->expectException(Exception::class);

        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = $this->createGraphQlSdk($curlStub);

        $orderId = $this->getRandomString();

        $response = new Response(
            400,
            json_encode($this->getOrderRefundsResponseData($orderId), JSON_THROW_ON_ERROR), []
        );

        $curlStub->method('request')->willReturn($response);

        $this->assertFalse($graphQlSdk->getOrderRefunds($orderId));
    }

    /**
     * @test
     *
     * @return void
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function assert_network_exception_on_5xx_response_code(): void
    {
        $this->expectException(NetworkException::class);

        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = $this->createGraphQlSdk($curlStub);

        $orderId = $this->getRandomString();

        $response = new Response(
            random_int(500, 599),
            json_encode($this->getOrderRefundsResponseData($orderId), JSON_THROW_ON_ERROR), []
        );

        $curlStub->method('request')->willReturn($response);

        $this->assertFalse($graphQlSdk->getOrderRefunds($orderId));
    }

    /**
     * @param string $orderId
     * @return array[]
     */
    private function getOrderRefundsResponseData(string $orderId): array
    {
        return [
            'data' => [
                'order' => [
                    'id' => $orderId,
                    'payments' => [
                        [
                            'id' => 'PAYXXXXXX',
                            'refunds' => [
                                [
                                    'id' => 'REXXXXX',
                                    'status' => 'PENDING/SUCCEEDED/FAILED',
                                    'reason' => 'Some Reason',
                                    'amount' => [
                                        'amount' => '10.00', // Keep as string otherwise decimal zeros are removed by PHP.
                                        'currency' => 'GBP'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
