<?php
declare(strict_types=1);

namespace Rvvup\Sdk\Rest;

use Rvvup\Api\Model\ShipmentTracking;
use Rvvup\Api\Model\ShipmentTrackingCreateInput;
use Rvvup\Api\ShipmentTrackingApi;
use Rvvup\ApiException;

class ShipmentTrackings
{
    /**
     * @var RvvupClient
     */
    private $client;

    /**
     * @var ShipmentTrackingApi
     */
    private $api;

    public function __construct(RvvupClient $client)
    {
        $this->client = $client;
        $this->api = new ShipmentTrackingApi(null, $client->configuration());
    }

    /**
     * @param string $paymentSessionId
     * @param ShipmentTrackingCreateInput $input
     * @return ShipmentTracking
     * @throws ApiException
     */
    public function create(string $paymentSessionId, ShipmentTrackingCreateInput $input): ShipmentTracking
    {
        return $this->api->createShipmentTracking($this->client->getMerchantId(), $paymentSessionId, $input);
    }

    /**
     * @param string $checkoutId
     * @param string $paymentSessionId
     * @param ShipmentTrackingCreateInput $input
     * @return ShipmentTracking
     * @throws ApiException
     */
    public function createWithCheckout(string $checkoutId, string $paymentSessionId, ShipmentTrackingCreateInput $input): ShipmentTracking
    {
        return $this->api->createShipmentTrackingWithCheckout($this->client->getMerchantId(), $paymentSessionId, $checkoutId, $input);
    }

}
