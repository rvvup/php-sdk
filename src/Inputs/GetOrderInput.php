<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Inputs;

/**
 * We should not create this class directly.
 * Use \Rvvup\Sdk\Factories\Inputs\GetOrderInput instead, to generate one.
 */
class GetOrderInput implements InputInterface
{
    /**
     * Rvvup's Order ID to fetch refunds for.
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
     * Get Rvvup's Order ID to fetch refunds for.
     *
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
