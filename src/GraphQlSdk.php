<?php declare(strict_types=1);

namespace Rvvup\Sdk;

use Rvvup\Sdk\Exceptions\ApiError;
use Rvvup\Sdk\Exceptions\NetworkException;
use Rvvup\Sdk\Inputs\RefundCreateInput;

class GraphQlSdk
{
    private const REDACTED = "***REDACTED***";
    /** @var string */
    private $endpoint;
    /** @var string */
    private $merchantId;
    /** @var string */
    private $authToken;
    /** @var string */
    private $userAgent;

    /**
     * An HTTP Client similar to Guzzle's HTTP Client.
     * ToDo: Refactor to use PSR-18 Interface
     *
     * @var Curl
     */
    private $adapter;

    /**
     * A Logger implementation (eg PSR Logger).
     *
     * @var null
     */
    private $logger;

    /**
     * Enable debug logging.
     *
     * @var bool
     */
    private $debug;

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
     * @param string|null $cartTotal
     * @param string|null $currency
     * @param array|null $inputOptions
     * @return array
     */
    public function getMethods(string $cartTotal = null, string $currency = null, array $inputOptions = null): array
    {
        $query = <<<'QUERY'
query merchant ($id: ID!, $total: MoneyInput) {
    merchant (id: $id) {
        paymentMethods (search: {includeInactive: false, total: $total}) {
            edges {
                node {
                    name
                    displayName
                    description
                    summaryUrl
                    logoUrl
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
                    }
                    captureType
                    settings {
                        assets {
                            assetType
                            url
                            attributes
                        }
                        ... on CardPaymentMethodSettings {
                        motoEnabled
                        liveStatus
                        initializationToken
                        flow
                        form {
                                translation {
                                    label {
                                        cardNumber
                                        expiryDate
                                        securityCode
                                    }
                                    button {
                                        pay
                                        processing
                                    }
                                    error {
                                        fieldRequired
                                        valueTooShort
                                        valueMismatch
                                    }
                                }
                            }
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
                    ... on ClearpayPaymentMethodSettings {
                            checkout {
                                theme {
                                    value
                                }
                                messaging {
                                    enabled
                                    iconType {
                                        value
                                    }
                                    modalTheme {
                                        value
                                    }
                                }
                            }
                            product {
                                theme {
                                    value
                                }
                                messaging {
                                    enabled
                                    iconType {
                                        value
                                    }
                                    modalTheme {
                                        value
                                    }
                                }
                            }
                            cart {
                                theme {
                                    value
                                }
                                messaging {
                                    enabled
                                    iconType {
                                        value
                                    }
                                    modalTheme {
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

        $total = null;

        if ($cartTotal !== null && $currency !== null) {
            $total = [
                "amount" => $cartTotal,
                "currency" => $currency,
            ];
        }

        $variables = [
            "id" => $this->merchantId,
            "total" => $total,
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
                "logoUrl" => $method["logoUrl"],
                "assets" => $method["assets"],
                "limits" => $method["limits"],
                "settings" => $method["settings"] ?? null,
                "captureType" => $method["captureType"],
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
    }
}
QUERY;
        return $this->doRequest($query, $orderData);
    }

    /**
     * @param $paymentData
     * @return mixed
     * @throws \Exception
     */
    public function createPayment($paymentData)
    {
        $query = <<<'QUERY'
mutation paymentCreate($input: PaymentCreateInput!) {
    paymentCreate(input: $input) {
        id
        summary {
            paymentActions {
                type
                method
                value
            }
        }
    }
}
QUERY;
        return $this->doRequest($query, $paymentData);
    }

    /**
     * @param string $paymentId
     * @param string $orderId
     * @param string $authorizationResponse
     * @param string|null $threeDSecureResponse
     * @return array|false
     * @throws \Exception
     */
    public function confirmCardAuthorization(
        string $paymentId,
        string $orderId,
        string $authorizationResponse,
        ?string $threeDSecureResponse
    ) {
        $query = <<<'QUERY'
        mutation cardAuthorizationConfirm ($input: CardAuthorizationConfirmInput!) {
            cardAuthorizationConfirm (input: $input) {
                authorizationId
            }
        }
QUERY;
        $variables = [
            "input" => [
                "merchantId" => $this->merchantId,
                "orderId" => $orderId,
                "paymentId" => $paymentId,
                "authorizationResponse" => $authorizationResponse,
                "threeDSecureResponse" => $threeDSecureResponse,
            ],
        ];

        return $this->doRequest($query, $variables)["data"]["cardAuthorizationConfirm"];
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function updateOrder($data)
    {
        $query = <<<'QUERY'
mutation OrderUpdate($input: OrderUpdateInput!){
    orderUpdate(input: $input) {
        id
        status
        redirectToCheckoutUrl
        dashboardUrl
    }
}
QUERY;
        return $this->doRequest($query, $data);
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
        dashboardUrl
        status
        payments {
            id
            status
            authorizationExpiresAt
            captureType
            ... on CardPayment {
            cvvResponseCode
            avsAddressResponseCode
            avsPostCodeResponseCode
            eci
            cavv
            acquirerResponseCode
            acquirerResponseMessage
            }
	    ... on ApplePayPayment {
            cvvResponseCode
            avsAddressResponseCode
            avsPostCodeResponseCode
            eci
            cavv
            acquirerResponseCode
            acquirerResponseMessage
            }
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

        if (is_array($response) && isset($response['data']['order'])) {
            return $response['data']['order'];
        }

        return false;
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
                    "amount" => (string) round($amount, 2),
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
     * @param string $orderId
     * @return false|mixed
     * @throws \Exception
     */
    public function isOrderVoidable(string $orderId)
    {
        $query = <<<'QUERY'
query order ($id: ID!, $merchant: IdInput!) {
    order (id: $id, merchant: $merchant) {
        paymentSummary {
            isVoidable
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
        if (is_array($response) && isset($response["data"]["order"]["paymentSummary"]["isVoidable"])) {
            return $response["data"]["order"]["paymentSummary"]["isVoidable"];
        }
        return false;
    }

    /**
     * @param string $orderId
     * @param string $paymentId
     * @param string|null $reason
     * @return false|mixed
     * @throws NetworkException
     * @throws \JsonException
     */
    public function voidPayment(string $orderId, string $paymentId, string $reason = null)
    {
        $query = <<<'QUERY'
mutation paymentVoid ($input: PaymentVoidInput!) {
    paymentVoid (input: $input) {
        status
    }
}
QUERY;
        $variables = [
            "input" => [
                "id" => $paymentId,
                "merchantId" => $this->merchantId,
                "orderId" => $orderId,
                'reason' => $reason,
                "idempotencyKey" => $paymentId . "_" . $orderId,
            ]
        ];
        $response = $this->doRequest($query, $variables);
        if (is_array($response) && isset($response["data"]["paymentVoid"]["status"])) {
            return $response["data"]["paymentVoid"]["status"];
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
     * @param \Rvvup\Sdk\Inputs\RefundCreateInput $input
     * @return array|false
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function refundCreate(RefundCreateInput $input)
    {
        $query = <<<'QUERY'
mutation refundCreate ($input: RefundCreateInput!) {
    refundCreate (input: $input) {
        id
        amount {
            amount
            currency
        }
        status
    }
}
QUERY;
        $variables = [
            "input" => [
                "orderId" => $input->getOrderId(),
                'merchantId' => $this->merchantId,
                'amount' => [
                    'amount' => (string) round((float) $input->getAmount(), 2),
                    'currency' => $input->getCurrency(),
                ],
                'reason' => $input->getReason(),
                'idempotencyKey' => $input->getIdempotencyKey(),
            ],
        ];

        $response = $this->doRequest($query, $variables);

        if (is_array($response) && isset($response['data']['refundCreate'])) {
            return $response['data']['refundCreate'];
        }

        return false;
    }

    /**
     * @param string $orderId
     * @return false|array = [
     *     'id' => 'Rvvup Order Id',
     *     'payments' => [
     *         [
     *             'id' => 'Rvvup Payment Id',
     *             'refunds' => [
     *                 'id' => 'Rvvup Refund Id',
     *                 'status' => 'Rvvup Refund Status',
     *                 'reason' => 'Rvvup Refund Reason',
     *                 'amount' => [
     *                     'amount' => '10.00',
     *                     'currency' => 'GBP'
     *                 ]
     *             ],
     *         ],
     *     ]
     * ]
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
     * @throws \Exception
     */
    public function getOrderRefunds(string $orderId)
    {
        $query = <<<'QUERY'
query order ($id: ID!, $merchant: IdInput!) {
    order (id: $id, merchant: $merchant) {
        id
        payments {
            id
            refunds {
                id
                status
                reason
                amount {
                    amount
                    currency
                }
            }
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

        return is_array($response) && isset($response['data']['order']['payments'])
            ? $response['data']['order']
            : false;
    }

    /**
     * @param string $paymentId
     * @param string $orderId
     * @return array|false
     * @throws \Exception
     */
    public function cancelPayment(string $paymentId, string $orderId)
    {
        $query = <<<'QUERY'
mutation paymentCancel ($input: PaymentCancelInput!) {
    paymentCancel (input: $input) {
        status
    }
}
QUERY;
        $variables = [
            "input" => [
                "id" => $paymentId,
                "merchantId" => $this->merchantId,
                "orderId" => $orderId,
                "idempotencyKey" => $paymentId . "_" . $orderId,
            ],
        ];

        return $this->doRequest($query, $variables)["data"]["paymentCancel"];
    }

    /**
     * @param string $orderId
     * @param string $paymentId
     * @return false|mixed
     * @throws NetworkException
     * @throws \JsonException
     */
    public function paymentCapture(string $orderId, string $paymentId)
    {
        $query = <<<'QUERY'
mutation paymentCapture ($input: PaymentCaptureInput!) {
    paymentCapture (input: $input) {
        status
    }
}
QUERY;
        $variables = [
            "input" => [
                "id" => $paymentId,
                "merchantId" => $this->merchantId,
                "orderId" => $orderId,
                "idempotencyKey" => $paymentId . "_" . $orderId,
            ]
        ];
        $response = $this->doRequest($query, $variables);
        if (is_array($response) && isset($response["data"]["paymentCapture"]["status"])) {
            return $response["data"]["paymentCapture"]["status"];
        }
        return false;
    }

    /**
     * @param $query
     * @param null $variables
     * @param array|null $inputOptions
     * @return mixed
     * @throws \Rvvup\Sdk\Exceptions\NetworkException
     * @throws \JsonException
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
            $processed = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
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
                $errorCode = $errors[0]["extensions"]["errorCode"] ?? "";

                throw new ApiError($errorString, $errorCode);
            }
            if ($this->debug) {
                $this->log("Successful GraphQL request", $debugData);
            }
            return $processed;
        }

        //Unexpected HTTP response code
        $this->log('Unexpected HTTP response code', $debugData);

        if ($responseCode >= 500 && $responseCode < 600) {
            throw new NetworkException(
                'There was a network error returned via the API. Please use the same idempotency if you retry.'
            );
        }

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
            if (in_array($key, $redactableKeys, true)) {
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
