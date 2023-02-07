<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Inputs;

class RefundCreateInput implements InputInterface
{
    /**
     * Rvvup's Order ID to Refund.
     *
     * @var string
     */
    private $orderId;

    /**
     * Amount to Refund.
     *
     * @var string
     */
    private $amount;

    /**
     * Currency of Refund.
     *
     * @var string
     */
    private $currency;

    /**
     * Idempotency key for request.
     *
     * @var string
     */
    private $idempotencyKey;

    /**
     * Reason for Refund.
     *
     * @var string
     */
    private $reason;

    /**
     * @param string $orderId
     * @param string $amount
     * @param string $currency
     * @param string $idempotencyKey
     * @param string $reason
     * @return void
     */
    public function __construct(
        string $orderId,
        string $amount,
        string $currency,
        string $idempotencyKey,
        string $reason
    ) {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->idempotencyKey = $idempotencyKey;
        $this->reason = $reason;
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

    /**
     * Get Amount to Refund.
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Get Currency of Refund.
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get Reason for Refund.
     *
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Get Idempotency key for request.
     *
     * @return string
     */
    public function getIdempotencyKey(): string
    {
        return $this->idempotencyKey;
    }
}
