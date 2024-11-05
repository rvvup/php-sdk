<?php
declare(strict_types=1);

namespace Rvvup\Sdk\Rest;

use Rvvup\Api\CheckoutsApi;
use Rvvup\Api\Model\Checkout;
use Rvvup\Api\Model\CheckoutCreateInput;
use Rvvup\Api\Model\CheckoutPage;
use Rvvup\Api\Model\PaymentMethodDetailsPage;
use Rvvup\ApiException;

class Checkouts
{
    /**
     * @var RvvupClient
     */
    private $client;

    /**
     * @var CheckoutsApi
     */
    private $api;

    public function __construct(RvvupClient $client)
    {
        $this->client = $client;
        $this->api = new CheckoutsApi(null, $client->configuration());
    }

    /**
     * @param CheckoutCreateInput $input
     * @param string|null $idempotencyKey
     * @return Checkout
     * @throws ApiException
     */
    public function create(CheckoutCreateInput $input, ?string $idempotencyKey): Checkout
    {
        return $this->api->createCheckout($this->client->getMerchantId(), $input, $idempotencyKey);
    }

    /**
     * @param string $id
     * @return Checkout
     * @throws ApiException
     */
    public function get(string $id): Checkout
    {
        return $this->api->getCheckout($id, $this->client->getMerchantId());
    }

    /**
     * @param string $offset
     * @param string $limit
     * @return CheckoutPage
     * @throws ApiException
     */
    public function list(string $offset, string $limit): CheckoutPage
    {
        return $this->api->listCheckouts($this->client->getMerchantId(), $offset, $limit);
    }


    /**
     * @param string $id
     * @param string $offset
     * @param string $limit
     * @return PaymentMethodDetailsPage
     * @throws ApiException
     */
    public function paymentMethods(string $id, string $offset, string $limit): PaymentMethodDetailsPage
    {
        return $this->api->listCheckoutPaymentMethods($id, $this->client->getMerchantId(), $offset, $limit);
    }

}
