<?php
declare(strict_types=1);

namespace Rvvup\Sdk\Rest;

use Rvvup\Api\Model\PaymentSession;
use Rvvup\Api\Model\PaymentSessionCreateInput;
use Rvvup\Api\PaymentSessionsApi;
use Rvvup\ApiException;

class PaymentSessions
{
    /**
     * @var RvvupClient
     */
    private $client;

    /**
     * @var PaymentSessionsApi
     */
    private $api;

    public function __construct(RvvupClient $client)
    {
        $this->client = $client;
        $this->api = new PaymentSessionsApi(null, $client->configuration());
    }

    /**
     * @param string $checkoutId
     * @param PaymentSessionCreateInput $input
     * @return PaymentSession
     * @throws ApiException
     */
    public function create(string $checkoutId, PaymentSessionCreateInput $input): PaymentSession
    {
        return $this->api->createPaymentSession($this->client->getMerchantId(), $checkoutId, $input);
    }

    /**
     * @param string $checkoutId
     * @param string $id
     * @return PaymentSession
     * @throws ApiException
     */
    public function get(string $checkoutId, string $id): PaymentSession
    {
        return $this->api->getPaymentSession($this->client->getMerchantId(), $checkoutId, $id);
    }
}
