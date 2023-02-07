<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Factories\Inputs;

use Rvvup\Sdk\Inputs\GetOrderInput;
use Rvvup\Sdk\Inputs\InputInterface;

/**
 * Factory to generate a GetOrderInput class. It should be used instead of the class itself.
 */
class GetOrderInputFactory
{
    /**
     * Get a new GetOrderInput model.
     *
     * @param string $orderId
     * @return \Rvvup\Sdk\Inputs\InputInterface|\Rvvup\Sdk\Inputs\GetOrderInput
     */
    public function create(string $orderId): InputInterface
    {
        return new GetOrderInput($orderId);
    }
}
