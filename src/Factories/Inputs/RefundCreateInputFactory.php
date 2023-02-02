<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Factories\Inputs;

use Rvvup\Sdk\Inputs\InputInterface;
use Rvvup\Sdk\Inputs\RefundCreateInput;

/**
 * Factory to generate a RefundCreateInput class. It should be used instead of the class itself.
 */
class RefundCreateInputFactory
{
    /**
     * Get a new RefundCreateInput model.
     *
     * @param string $orderId
     * @param string $amount
     * @param string $currency
     * @param string $idempotencyKey
     * @param string|null $reason
     * @return \Rvvup\Sdk\Inputs\InputInterface|\Rvvup\Sdk\Inputs\RefundCreateInput
     */
    public function create(
        string $orderId,
        string $amount,
        string $currency,
        string $idempotencyKey,
        ?string $reason = null
    ): InputInterface {
        return new RefundCreateInput($orderId, $amount, $currency, $idempotencyKey, $reason ?? '');
    }
}