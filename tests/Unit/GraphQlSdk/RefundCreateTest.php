<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\GraphQlSdk;

use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\Curl;
use Rvvup\Sdk\Factories\Inputs\RefundCreateInputFactory;
use Rvvup\Sdk\GraphQlSdk;
use Rvvup\Sdk\Inputs\RefundCreateInput;
use Rvvup\Sdk\Response;

/**
 * @test
 * @group refund
 */
class RefundCreateTest extends TestCase
{
    /**
     * @test
     * @group refund
     *
     * @return void
     * @throws \Exception
     */
    public function assert_successful_refund_call(): void
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

        $data = [
            'data' => [
                'refundCreate' => [
                    'id' => 'REXXXXXXX',
                    'amount' => [
                        'amount' => 10.00,
                        'currency' => 'GBP'
                    ],
                    'status' => 'SUCCESSFUL'
                ]
            ]
        ];

        $response = new Response(200, json_encode($data, JSON_THROW_ON_ERROR), []);

        $curlStub->method('request')->willReturn($response);

        $inputFactory = new RefundCreateInputFactory();

        $this->assertIsArray($graphQlSdk->refundCreate($inputFactory->create(
            'ORXXXX',
            '0000',
            'GBP',
            'KEY',
            'RANDOM REASON'
        )));
    }
}
