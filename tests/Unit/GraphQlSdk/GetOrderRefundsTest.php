<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\GraphQlSdk;

use Exception;
use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\Curl;
use Rvvup\Sdk\Exceptions\NetworkException;
use Rvvup\Sdk\Factories\Inputs\GetOrderInputFactory;
use Rvvup\Sdk\GraphQlSdk;
use Rvvup\Sdk\Response;

/**
 * @test
 * @group order
 * @group refund
 */
class GetOrderRefundsTest extends TestCase
{
    private $getOrderRefundsData = [
        'data' => [
            'order' => [
                'id' => 'REXXXXXXX',
                'payments' => [
                    [
                        'id' => 'PAYXXXXXX',
                        'refunds' => [
                            [
                                'id' => 'REXXXXX',

                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

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

        $graphQlSdk = new GraphQlSdk(
            'https://endpoint.com/url',
            'MEXXXXXXX',
            'AUTH_TOKEN',
            'USER_AGENT',
            $curlStub,
            null,
            false
        );

        $response = new Response(200, json_encode($this->getOrderRefundsData, JSON_THROW_ON_ERROR), []);

        $curlStub->method('request')->willReturn($response);

        $inputFactory = new GetOrderInputFactory();

        $this->assertIsArray($graphQlSdk->getOrderRefunds($inputFactory->create('ORXXXX')));
    }

    /**
     * @test
     *
     * @return void
     * @throws \JsonException
     */
    public function assert_false_on_empty_response(): void
    {
        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = new GraphQlSdk(
            'https://endpoint.com/url',
            'MEXXXXXXX',
            'AUTH_TOKEN',
            'USER_AGENT',
            $curlStub,
            null,
            false
        );

        $response = new Response(200, json_encode([], JSON_THROW_ON_ERROR), []);

        $curlStub->method('request')->willReturn($response);

        $inputFactory = new GetOrderInputFactory();

        $this->assertFalse($graphQlSdk->getOrderRefunds($inputFactory->create('ORXXXX')));
    }

    /**
     * @test
     *
     * @return void
     * @throws \JsonException
     * @throws Exception
     */
    public function assert_exception_on_non_2xx_response_code(): void
    {
        $this->expectException(Exception::class);

        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = new GraphQlSdk(
            'https://endpoint.com/url',
            'MEXXXXXXX',
            'AUTH_TOKEN',
            'USER_AGENT',
            $curlStub,
            null,
            false
        );

        $response = new Response(400, json_encode($this->getOrderRefundsData, JSON_THROW_ON_ERROR), []);

        $curlStub->method('request')->willReturn($response);

        $inputFactory = new GetOrderInputFactory();

        $this->assertFalse($graphQlSdk->getOrderRefunds($inputFactory->create('ORXXXX')));
    }

    /**
     * @test
     *
     * @return void
     * @throws \JsonException
     * @throws \Exception
     */
    public function assert_network_exception_on_5xx_response_code(): void
    {
        $this->expectException(NetworkException::class);

        $curlStub = $this->createStub(Curl::class);

        $graphQlSdk = new GraphQlSdk(
            'https://endpoint.com/url',
            'MEXXXXXXX',
            'AUTH_TOKEN',
            'USER_AGENT',
            $curlStub,
            null,
            false
        );

        $response = new Response(random_int(500, 599), json_encode($this->getOrderRefundsData, JSON_THROW_ON_ERROR), []);

        $curlStub->method('request')->willReturn($response);

        $inputFactory = new GetOrderInputFactory();

        $this->assertFalse($graphQlSdk->getOrderRefunds($inputFactory->create('ORXXXX')));
    }
}
