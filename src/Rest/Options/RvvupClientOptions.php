<?php

namespace Rvvup\Sdk\Rest\Options;

class RvvupClientOptions
{
    /**
     * @var string|null
     */
    private $baseUrl;

    /**
     * @var string|null
     */
    private $merchantId;

    /**
     * @var string|null
     */
    private $userAgent;

    /**
     * @param string|null $baseUrl
     * @param string|null $merchantId
     */
    public function __construct(string $baseUrl = null, string $merchantId = null, string $userAgent = null)
    {
        $this->baseUrl = $baseUrl;
        $this->merchantId = $merchantId;
        $this->userAgent = $userAgent;
    }

    public function getBaseUrl(): ?string
    {
        return $this->baseUrl;
    }

    public function getMerchantId(): ?string
    {
        return $this->merchantId;
    }


    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setBaseUrl(?string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    public function setMerchantId(?string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }



}
