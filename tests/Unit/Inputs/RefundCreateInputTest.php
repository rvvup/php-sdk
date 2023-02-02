<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\Inputs;

use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\Inputs\RefundCreateInput;
use Rvvup\Sdk\Tests\HelperTrait;

/**
 * @group refund
 * @group input
 */
class RefundCreateInputTest extends TestCase
{
    use HelperTrait;

    /**
     * @test
     * @group refund
     * @group input
     *
     * @return void
     * @throws \Exception
     */
    public function assert_order_id_argument_is_set(): void
    {
        $orderId = $this->getRandomString();
        $input = new RefundCreateInput($orderId, '00', 'GBP', 'KEY', 'Reason');
        $this->assertEquals($orderId, $input->getOrderId());
    }

    /**
     * @test
     * @group refund
     * @group input
     *
     * @return void
     * @throws \Exception
     */
    public function assert_amount_argument_is_set(): void
    {
        $amount = $this->getRandomNumber();
        $input = new RefundCreateInput('ORDASSA', (string) $amount, 'GBP', 'KEY', 'Reason');
        $this->assertEquals((string) $amount, $input->getAmount());
    }

    /**
     * @test
     * @group refund
     * @group input
     *
     * @return void
     * @throws \Exception
     */
    public function assert_currency_argument_is_set(): void
    {
        $currency = $this->getRandomString(3);
        $input = new RefundCreateInput('ORDASSA', '000', $currency, 'KEY', 'Reason');
        $this->assertEquals($currency, $input->getCurrency());
    }

    /**
     * @test
     * @group refund
     * @group input
     *
     * @return void
     * @throws \Exception
     */
    public function assert_idempotency_key_argument_is_set(): void
    {
        $idempotencyKey = $this->getRandomString();
        $input = new RefundCreateInput('ORDASSA', '000', 'GBP', $idempotencyKey, 'Reason');
        $this->assertEquals($idempotencyKey, $input->getIdempotencyKey());
    }

    /**
     * @test
     * @group refund
     * @group input
     *
     * @return void
     * @throws \Exception
     */
    public function assert_reason_argument_is_set(): void
    {
        $reason = $this->getRandomString();
        $input = new RefundCreateInput('ORDASSA', '000', 'GBP', 'KEY', $reason);
        $this->assertEquals($reason, $input->getReason());
    }
}
