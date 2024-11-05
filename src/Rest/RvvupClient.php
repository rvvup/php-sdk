<?php
declare(strict_types=1);

namespace Rvvup\Sdk\Rest;

use Exception;
use Rvvup\Configuration;
use Rvvup\Sdk\Rest\Options\RvvupClientOptions;

class RvvupClient
{

    /**
     * @var RvvupClientOptions
     */
    private $options;

    /**
     * @var string
     */
    private $authToken;

    /**
     * @var Checkouts
     */
    private $checkouts;

    /**
     * $var PaymentSessions
     */
    private $paymentSessions;

    /**
     * @throws Exception
     */
    public function __construct(string $authToken, ?RvvupClientOptions $options)
    {
        $this->authToken = $authToken;
        $jwt = $this->decodeJwt($authToken);
        if ($jwt == null) {
            throw new \Exception("Invalid auth token");
        }
        $this->options = $options ?? new RvvupClientOptions();
        if ($this->options->getBaseUrl() == null) {
            $this->options->setBaseUrl(str_replace('/graphql', '', $jwt['aud']));
        }
        if ($this->options->getMerchantId() == null) {
            $this->options->setMerchantId($jwt['merchantId']);
        }
        if ($this->options->getUserAgent() == null) {
            $this->options->setUserAgent('RvvupSDK');
        }


        $this->checkouts = new Checkouts($this);
        $this->paymentSessions = new PaymentSessions($this);
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->options->getMerchantId();
    }

    /**
     * @return Checkouts
     */
    public function checkouts(): Checkouts
    {
        return $this->checkouts;
    }

    /**
     * @return PaymentSessions
     */
    public function paymentSessions(): PaymentSessions
    {
        return $this->paymentSessions;
    }

    /**
     * @return Configuration
     */
    public function configuration(): Configuration
    {
        return Configuration::getDefaultConfiguration()
            ->setAccessToken($this->authToken)
            ->setHost($this->options->getBaseUrl())
            ->setUserAgent($this->options->getUserAgent());
    }


    private function decodeJwt(string $jwt): ?array
    {
        $parts = explode('.', $jwt);
        if (!$parts || count($parts) <= 1) {
            return null;
        }
        $payload = $parts[1];
        if ($payload == null) {
            return null;
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        return json_decode(base64_decode($payload), true);
    }
}
