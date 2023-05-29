<?php

namespace Raw\Webshop\Tests;

use Log;
use Stripe\HttpClient\ClientInterface;

class MockStripeClient implements ClientInterface
{
    public $paramsCache  = [];

    public function getParamsByType($type)
    {
        return $this->paramsCache[$type];
    }

    public function clear()
    {
        $this->paramsCache = [];
    }

    public function request($method, $absUrl, $headers, $params, $hasFile): array
    {
        $type = null;
        $body = null;
        Log::debug($absUrl);
        switch ($absUrl) {
            case "https://api.stripe.com/v1/payment_intents":
            case "https://api.stripe.com/v1/payment_intents/test-payment-id":
                $type = "payment_intents";
                $body = $this->paymentIntentResponse();
                break;

            case "https://api.stripe.com/v1/charges":
            case "https://api.stripe.com/v1/charges/test-charge-id":
                $type = "charges";
                $body = $this->chargeResponse();
                break;

            case "https://api.stripe.com/v1/refunds":
                $type = "refunds";
                $body = $this->refundResponse();
                break;

            case "https://api.stripe.com/v1/checkout/sessions":
                $type = "sessions";
                $body = $this->createSessionResponse($params);
                break;

            default:
                throw new \Exception("MockStripeClient unknown url: " . $absUrl);
        }

        // Cache the last used parameters so it can be retrieved during testing
        $this->paramsCache[$type] = $params;
        Log::debug(json_encode($this->paramsCache));

        return [$body, 200, []];
    }

    private function paymentIntentResponse(): string
    {
        return json_encode([
            "id" => "test-payment-id",
            "charges_enabled" => true,
            "charges" => [
                "object" => "list",
                "data" => [[
                    "id" => "test-charge-id"
                ]],
                "has_more" => false,
                "url" => "/v1/charges?payment_intent=pi_1EUnTQ225jfF0VNw0l9O1DCg"
            ],
        ]);
    }

    private function chargeResponse(): string
    {
        return json_encode([
            "id" => "test-charge-id",
            "object" => "charge",
            "amount" => 5555500,
            "amount_captured" => 5555500,
            "amount_refunded" => 0,
            "application" => null,
            "application_fee" => null,
            "application_fee_amount" => null,
            "balance_transaction" => "test-balance-transaction-id",
            "billing_details" => [
                "address" => [
                    "city" => null,
                    "country" => null,
                    "line1" => null,
                    "line2" => null,
                    "postal_code" => null,
                    "state" => null
                ],
                "email" => null,
                "name" => null,
                "phone" => null
            ],
            "calculated_statement_descriptor" => "PLANTGEKKIES",
            "captured" => true,
            "created" => 1646082366,
            "currency" => "eur",
            "customer" => null,
            "description" => "test funds",
            "disputed" => false,
            "failure_balance_transaction" => null,
            "failure_code" => null,
            "failure_message" => null,
            "fraud_details" => [],
            "invoice" => null,
            "livemode" => false,
            "metadata" => [],
            "on_behalf_of" => null,
            "outcome" => [
                "network_status" => "approved_by_network",
                "reason" => null,
                "risk_level" => "normal",
                "risk_score" => 29,
                "seller_message" => "Payment complete.",
                "type" => "authorized"
            ],
            "paid" => true,
            "payment_intent" => "pi_3KYGPxKnEV82zsZa0JciHCiC",
            "payment_method" => "src_1KYGPwKnEV82zsZaXd5MyGbc",
            "payment_method_details" => [
                "card" => [
                    "brand" => "visa",
                    "checks" => [
                        "address_line1_check" => null,
                        "address_postal_code_check" => null,
                        "cvc_check" => "pass"
                    ],
                    "country" => "US",
                    "exp_month" => 11,
                    "exp_year" => 2022,
                    "fingerprint" => "luxrtUccBGJXAxrK",
                    "funding" => "debit",
                    "installments" => null,
                    "last4" => "5556",
                    "mandate" => null,
                    "network" => "visa",
                    "three_d_secure" => null,
                    "wallet" => null
                ],
                "type" => "card"
            ],
            "receipt_email" => null,
            "receipt_number" => null,
            "receipt_url" => "https =>//pay.stripe.com/receipts/acct_1KO5GLKnEV82zsZa/ch_3KYGPxKnEV82zsZa0F8tPqPJ/rcpt_LEjt2f0BVawkk8XaMHMZyJ4PsRAdDfA",
            "refunded" => false,
            "refunds" => [
                "object" => "list",
                "data" => [],
                "has_more" => false,
                "url" => "/v1/charges/ch_3KYGPxKnEV82zsZa0F8tPqPJ/refunds"
            ],
            "review" => null,
            "shipping" => null,
            "source_transfer" => null,
            "statement_descriptor" => "PLANTGEKKIES",
            "statement_descriptor_suffix" => null,
            "status" => "succeeded",
            "transfer_data" => null,
            "transfer_group" => null
        ]);
    }

    private function createSessionResponse(): string
    {
        return json_encode([
            "id" => "cs_test_a1e7jR2VU2vG7obnfVwESNEk0db5gIwN1kvAfbXTOxBfRcR39Am9YNA7kT",
            "object" => "checkout.session",
            "url" => "https://checkout.stripe.com/c/pay"
        ]);
    }
}
