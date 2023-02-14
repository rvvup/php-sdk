<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\Factories\Inputs;

use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\Factories\Inputs\RefundCreateInputFactory;
use Rvvup\Sdk\Inputs\RefundCreateInput;

/**
 * @group factory
 * @group refund
 * @group input
 */
class RefundCreateInputFactoryTest extends TestCase
{
    /**
     * Assert that the returned input interface is of RefundCreateInput
     *
     * @test
     * @group factory
     * @group order
     * @group refund
     * @group input
     *
     * @return void
     */
    public function assert_returned_input_interface_is_refund_create_input(): void
    {
        $factory = new RefundCreateInputFactory();

        $this->assertInstanceOf(
            RefundCreateInput::class,
            $factory->create('ORXXXX', '0000', 'GBP', 'KEY', null)
        );
    }

    /**
     * Assert that if we use the factory to create the RefundCreateInput object
     * and we provide null for Reason
     * the returned created RefundCreateInput reason is an empty string.
     *
     * @test
     * @group factory
     * @group refund
     * @group input
     * @return void
     */
    public function assert_created_input_reason_property_is_empty_string_if_provided_reason_argument_is_null(): void
    {
        $factory = new RefundCreateInputFactory();

        $this->assertEmpty($factory->create('ORXXXX', '0000', 'GBP', 'KEY', null)->getReason());
    }
}
