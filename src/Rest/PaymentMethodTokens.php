<?php
declare(strict_types=1);

namespace Rvvup\Sdk\Rest;

use Rvvup\Api\PaymentMethodTokensApi;
use Rvvup\ApiException;

class PaymentMethodTokens
{
    /** @var RvvupClient */
    private $client;

    /** @var PaymentMethodTokensApi */
    private $api;

    public function __construct(RvvupClient $client)
    {
        $this->client = $client;
        $this->api = new PaymentMethodTokensApi(null, $client->configuration());
    }

    /**
     * Returns false when the token was not found (already revoked or never existed).
     *
     * @param string $tokenId
     * @return bool
     * @throws ApiException on non-404 errors
     */
    public function revoke(string $tokenId): bool
    {
        try {
            $this->api->revokePaymentMethodToken($this->client->getMerchantId(), $tokenId);
            return true;
        } catch (ApiException $e) {
            if ($e->getCode() === 404) {
                return false;
            }
            throw $e;
        }
    }
}
