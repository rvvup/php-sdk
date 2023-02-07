<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\Factories\Inputs;

use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\Factories\Inputs\GetOrderInputFactory;
use Rvvup\Sdk\Inputs\GetOrderInput;

/**
 * @group factory
 * @group order
 * @group refund
 * @group input
 */
class GetOrderInputFactoryTest extends TestCase
{
    /**
     * Assert that the returned input interface is of GetOrderInput
     *
     * @test
     * @group factory
     * @group order
     * @group refund
     * @group input
     *
     * @return void
     */
    public function assert_returned_input_interface_is_get_order_input(): void
    {
        $factory = new GetOrderInputFactory();

        $this->assertInstanceOf(GetOrderInput::class, $factory->create('ORXXX'));
    }
}
