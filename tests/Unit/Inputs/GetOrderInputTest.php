<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\Inputs;

use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\Inputs\GetOrderInput;
use Rvvup\Sdk\Tests\HelperTrait;

/**
 * @group order
 * @group refund
 * @group input
 */
class GetOrderInputTest extends TestCase
{
    use HelperTrait;

    /**
     * @test
     * @group order
     * @group refund
     * @group input
     *
     * @return void
     * @throws \Exception
     */
    public function assert_get_order_input_order_id_argument_is_set(): void
    {
        $orderId = $this->getRandomString();
        $input = new GetOrderInput($orderId);
        $this->assertEquals($orderId, $input->getOrderId());
    }
}
