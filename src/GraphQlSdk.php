<?php declare(strict_types=1);

namespace Rvvup\Sdk;

class GraphQlSdk
{
    private const REDACTED = "***REDACTED***";
    /** @var string */
    private $endpoint;
    /** @var string */
    private $merchantId;
    /** @var string */
    private $authToken;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var bool */
    private $debug;
    /** @var string */
    private $userAgent;
    /** @var Curl */
    private $adapter;

    /**
     * @param string $endpoint
     * @param string $merchantId
     * @param string $authToken
     * @param string $userAgent
     * @param $adapter
     * @param null $logger
     * @param bool $debug
     */
    public function __construct(
        string $endpoint,
        string $merchantId,
        string $authToken,
        string $userAgent,
        $adapter,
        $logger = null,
        bool $debug = false
    ) {
        if (!$merchantId || !$authToken || !$endpoint) {
            throw new \InvalidArgumentException("Unable to initialize Rvvup SDK, missing init parameters");
        }
        $this->endpoint = $endpoint;
        $this->merchantId = $merchantId;
        $this->authToken = $authToken;
        $this->userAgent = $userAgent;
        $this->logger = $logger;
        $this->debug = $debug;
        $this->adapter = $adapter;
    }

    /**
     * @param string $cartTotal
     * @param string $currency
     * @param array|null $inputOptions
     * @return array
     */
    public function getMethods(string $cartTotal, string $currency, array $inputOptions = null): array
    {
        $query = <<<'QUERY'
query merchant ($id: ID!, $total: MoneyInput!) {
    merchant (id: $id) {
        paymentMethods (search: {includeInactive: false, total: $total}) {
            edges {
                node {
                    name,
                    displayName,
                    description,
                    summaryUrl
                    assets {
                        assetType
                        url
                        attributes
                    }
                    limits {
                        total {
                            min
                            max
                            currency
                        }
                        expiresAt
                    },
                    settings {
                         assets {
                            assetType
                            url
                            attributes
                         }
                         ... on PaypalPaymentMethodSettings {
                             checkout {
                                 button {
                                     enabled
                                     layout {
                                         value
                                     }
                                     color {
                                         value
                                     }
                                     shape {
                                         value
                                     }
                                     label {
                                         value
                                     }
                                     tagline
                                     size
                                 }
                                 payLaterMessaging {
                                     enabled
                                     layout {
                                         value
                                     }
                                     logoType {
                                         value
                                     }
                                     logoPosition {
                                         value
                                     }
                                     textColor {
                                         value
                                     }
                                     textSize
                                     textAlignment {
                                         value
                                     }
                                     color {
                                         value
                                     }
                                     ratio {
                                         value
                                     }
                                 }
                             }
                             product {
                                 button {
                                     enabled
                                     layout {
                                         value
                                     }
                                     color {
                                         value
                                     }
                                     shape {
                                         value
                                     }
                                     label {
                                         value
                                     }
                                     tagline
                                     size
                                 }
                                 payLaterMessaging {
                                     enabled
                                     layout {
                                         value
                                     }
                                     logoType {
                                         value
                                     }
                                     logoPosition {
                                         value
                                     }
                                     textColor {
                                         value
                                     }
                                     textSize
                                     textAlignment {
                                         value
                                     }
                                     color {
                                         value
                                     }
                                     ratio {
                                         value
                                     }
                                 }
                             }
                         }
                    }
                }
            }
        }
    }
}
QUERY;
        $variables = [
            "id" => $this->merchantId,
            "total" => [
                "amount" => $cartTotal,
                "currency" => $currency,
            ],
        ];
        try {
            $response = $this->doRequest($query, $variables, $inputOptions);
        } catch (\Exception $e) {
            return [];
        }
        $responseMethods = $response["data"]["merchant"]["paymentMethods"]["edges"];
        $methods = [];
        foreach ($responseMethods as $responseMethod) {
            $method = $responseMethod["node"];
            $methods[] = [
                "name" => $method["name"],
                "displayName" => $method["displayName"],
                "description" => $method["description"],
                "summaryUrl" => $method["summaryUrl"],
                "assets" => $method["assets"],
                "limits" => $method["limits"],
            ];
        }
        return $methods;
    }

    /**
     * @param $orderData
     * @return mixed
     * @throws \Exception
     */
    public function createOrder($orderData)
    {
        $query = <<<'QUERY'
mutation OrderCreate($input: OrderCreateInput!) {
    orderCreate(input: $input) {
        id
        status
        redirectToCheckoutUrl
        dashboardUrl
        paymentSummary {
            paymentActions {
                type
                method
                value
            }
        }
    }
}
QUERY;
        return $this->doRequest($query, $orderData);
    }

    /**
     * Update Express type Order.
     *
     * @param $orderData
     * @return false|mixed
     * @throws \Exception
     */
    public function updateExpressOrder($orderData)
    {
        $query = <<<'QUERY'
mutation orderExpressUpdate ($input: OrderExpressUpdateInput!) {
    orderExpressUpdate(input: $input) {
        id
        type
        externalReference
        status
        dashboardUrl
        paymentSummary {
            paymentActions {
                type
                method
                value
            }
        }
    }
}
QUERY;
        $response = $this->doRequest($query, $orderData);

        return is_array($response) && isset($response['data']['orderExpressUpdate'])
            ? $response['data']['orderExpressUpdate']
            : false;
    }

    /**
     * @param string $orderId
     * @return false|mixed
     * @throws \Exception
     */
    public function getOrder(string $orderId)
    {
        $query = <<<'QUERY'
query order ($id: ID!, $merchant: IdInput!) {
    order (id: $id, merchant: $merchant) {
        id
        type
        externalReference
        total {
            amount
            currency
        }
        redirectToStoreUrl
        redirectToCheckoutUrl
        status
        dashboardUrl
    }
}
QUERY;
        $variables = [
            "id" => $orderId,
            "merchant" => [
                "id" => $this->merchantId,
            ],
        ];

        $response = $this->doRequest($query, $variables);

        return is_array($response) && isset($response['data']['order']) ? $response['data']['order'] : false;
    }

    /**
     * @param string $orderId
     * @return false|mixed
     * @throws \Exception
     */
    public function isOrderRefundable(string $orderId)
    {
        $query = <<<'QUERY'
query order ($id: ID!, $merchant: IdInput!) {
    order (id: $id, merchant: $merchant) {
        paymentSummary {
            isRefundable
        }
    }
}
QUERY;
        $variables = [
            "id" => $orderId,
            "merchant" => [
                "id" => $this->merchantId,
            ],
        ];
        $response = $this->doRequest($query, $variables);
        if (is_array($response) && isset($response["data"]["order"]["paymentSummary"]["isRefundable"])) {
            return $response["data"]["order"]["paymentSummary"]["isRefundable"];
        }
        return false;
    }

    /**
     * @param $orderId
     * @param $amount
     * @param $reason
     * @param $idempotency
     * @return false|mixed
     * @throws \Exception
     */
    public function refundOrder($orderId, $amount, $reason, $idempotency)
    {
        $query = <<<'QUERY'
mutation orderRefund ($input: OrderRefundInput!) {
    orderRefund (input: $input) {
        id
        externalReference
        payments {
          refunds {
            id
            status
            reason
          }
        }
    }
}
QUERY;
        $variables = [
            "input" => [
                "id" => $orderId,
                "merchant" => [
                    "id" => $this->merchantId,
                ],
                "amount" => [
                    "amount" => $amount,
                    "currency" => "GBP",
                ],
                "reason" => $reason,
                "idempotencyKey" => $idempotency,
            ],
        ];
        $response = $this->doRequest($query, $variables);

        if (is_array($response) && isset($response["data"]["orderRefund"])) {
            return $response["data"]["orderRefund"];
        }
        return false;
    }

    /**
     * Check if current credentials are valid and working
     *
     * @return bool
     * @throws \Exception
     */
    public function ping(): bool
    {
        $query = <<<QUERY
query ping {
  ping {
    pong
  }
}
QUERY;
        $response = $this->doRequest($query);

        return is_array($response) && isset($response["data"]["ping"]["pong"]);
    }

    /**
     * Update the webhook URL in the payments backend
     *
     * @param string $url
     * @return void
     * @throws \Exception
     */
    public function registerWebhook(string $url): void
    {
        $query = <<<'QUERY'
mutation merchantWebhookCreate($input: WebhookCreateInput!) {
	merchantWebhookCreate(input: $input) {
		url
	}
}
QUERY;
        $variables = [
            "input" => [
                "url" => $url,
                "merchant" => [
                    "id" => $this->merchantId,
                ],
            ],
        ];

        $response = $this->doRequest($query, $variables);
        if (isset($response["data"]["merchantWebhookCreate"]["url"]) &&
            $response["data"]["merchantWebhookCreate"]["url"] === $url) {
            return;
        }
        throw new \Exception('Response does not match specified URL');
    }

    /**
     * Create an Event Log record via the API.
     *
     * @param string $eventType
     * @param string $reason
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function createEvent(string $eventType, string $reason, array $data = []): void
    {
        $query = <<<'QUERY'
mutation eventCreate($input: AuditLogCreateInput!) {
    eventCreate(input: $input) {
        id
    }
}
QUERY;

        $variables = [
            "input" => [
                "actionType" => $eventType,
                "merchant" => [
                    "id" => $this->merchantId,
                ],
                "resourceId" => $this->merchantId, // The resource the event refers to (order, merchant etc)
                "reason" => $reason,
                "currentData" => $data,
            ],
        ];

        $this->doRequest($query, $variables);
    }

    /**
     * @param $query
     * @param null $variables
     * @param array|null $inputOptions
     * @return mixed
     * @throws \Exception
     */
    private function doRequest($query, $variables = null, array $inputOptions = null)
    {
        $data = ["query" => $query];
        if ($variables !== null) {
            $data["variables"] = $variables;
        }
        $options = [
            "json" => $data,
            "headers" => [
                "Content-Type" => "application/json; charset=utf-8",
                "Accept" => "application/json",
                "Authorization" => "Basic " . $this->authToken,
                "User-Agent" => $this->userAgent,
            ],
        ];
        if ($inputOptions !== null) {
            $options = array_merge($options, $inputOptions);
        }
        $response = $this->adapter->request("POST", $this->endpoint, $options);
        $request = $this->sanitiseRequestBody($data);
        $body = (string) $response->getBody();
        $responseCode = $response->getStatusCode();
        $debugData = [
            'code' => $responseCode,
            'requestHeaders' => $this->sanitiseRequestHeaders($options['headers']),
            'requestBody' => $request,
            'responseHeaders' => $this->formatResponseHeaders($response),
            'responseBody' => $body,
        ];

        if ($responseCode === 200) {
            $processed = json_decode($body, true);
            if (isset($processed["errors"])) {
                $this->log("GraphQL response error", $debugData);
                $errors = $processed["errors"];
                if (count($errors) > 1) {
                    $errorString = '';
                    foreach ($errors as $key => $error) {
                        $errorString .= sprintf('%s: %s', ++$key, $error["message"]);
                    }
                } else {
                    $errorString = $errors[0]["message"];
                }
                throw new \Exception($errorString);
            }
            if ($this->debug) {
                $this->log("Successful GraphQL request", $debugData);
            }
            return $processed;
        }
        //Unexpected HTTP response code
        $this->log('Unexpected HTTP response code', $debugData);
        throw new \Exception("Unexpected HTTP response code");
    }

    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    private function log(string $message, array $context): void
    {
        if ($this->logger) {
            $this->logger->debug($message, $context);
        }
    }

    /**
     * @param array $request
     * @return array
     */
    private function sanitiseRequestBody(array $request): array
    {
        $redactableKeys = ["customer", "billingAddress", "shippingAddress"];
        if (!isset($request["variables"]["input"])) {
            return $request;
        }
        foreach ($request["variables"]["input"] as $key => $value) {
            if (in_array($key, $redactableKeys)) {
                $request["variables"]["input"][$key] = self::REDACTED;
            }
        }
        return $request;
    }


    /**
     * @param array $headers
     * @return array
     */
    private function sanitiseRequestHeaders(array $headers): array
    {
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'authorization') {
                $headers[$key] = self::REDACTED;
            }
        }
        return $headers;
    }

    /**
     * @param $response
     * @return string
     */
    private function formatResponseHeaders($response): string
    {
        $headers = $response->getHeaders();
        $headers = is_array($headers) ? $headers : [];
        $formattedHeaders = "";
        foreach ($headers as $type => $header) {
            foreach ($header as $line) {
                $formattedHeaders .= "$type: $line" . PHP_EOL;
            }
        }
        return $formattedHeaders;
    }
}
