<?php
declare(strict_types=1);

namespace Rvvup\Sdk\Rest;

use Rvvup\Api\CheckoutTemplatesApi;
use Rvvup\Api\Model\CheckoutTemplate;
use Rvvup\Api\Model\CheckoutTemplateCreateInput;
use Rvvup\Api\Model\CheckoutTemplatePage;
use Rvvup\Api\Model\CheckoutTemplateUpdateInput;
use Rvvup\ApiException;

class CheckoutTemplates
{
    /**
     * @var RvvupClient
     */
    private $client;

    /**
     * @var CheckoutTemplatesApi
     */
    private $api;

    public function __construct(RvvupClient $client)
    {
        $this->client = $client;
        $this->api = new CheckoutTemplatesApi(null, $client->configuration());
    }

    /**
     * @param CheckoutTemplateCreateInput $input
     * @return CheckoutTemplate
     * @throws ApiException
     */
    public function create(CheckoutTemplateCreateInput $input): CheckoutTemplate
    {
        return $this->api->createCheckoutTemplate($this->client->getMerchantId(), $input);
    }

    /**
     * @param string $id
     * @param CheckoutTemplateUpdateInput $input
     * @return CheckoutTemplate
     * @throws ApiException
     */
    public function update(string $id, CheckoutTemplateUpdateInput $input): CheckoutTemplate
    {
        return $this->api->updateCheckoutTemplate($id, $this->client->getMerchantId(), $input);
    }

    /**
     * @param string $id
     * @return CheckoutTemplate
     * @throws ApiException
     */
    public function get(string $id): CheckoutTemplate
    {
        return $this->api->getCheckoutTemplate($id, $this->client->getMerchantId());
    }

    /**
     * @param string $offset
     * @param string $limit
     * @return CheckoutTemplatePage
     * @throws ApiException
     */
    public function list(string $offset, string $limit): CheckoutTemplatePage
    {
        return $this->api->listCheckoutTemplates($this->client->getMerchantId(), $offset, $limit);
    }
}
