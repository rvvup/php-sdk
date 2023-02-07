<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Inputs;

class GetOrderInput implements InputInterface
{
    /**
     * Rvvup's Order ID to Refund.
     *
     * @var string
     */
    private $orderId;

    /**
     * @param string $orderId
     * @return void
     */
    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Get Rvvup's Order ID to Refund.
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
