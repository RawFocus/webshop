<?php

namespace RawFocus\Webshop\Http\Controllers\Api;

use Log;
use Exception;
use WebshopOrders;
use WebshopPayments;

use Stripe\Stripe;
use Stripe\StripeObject;
use Stripe\Event as StripeEvent;

use RawFocus\Webshop\Enums\PaymentStatusEnum;

use RawFocus\Webshop\Events\Orders\PaymentReceived;

use RawFocus\Webshop\Http\Controllers\Controller;
use RawFocus\Webshop\Http\Requests\Checkout\StripeWebhookRequest;

class StripeController extends Controller
{
    /**
     * Handle the checkout event
     *
     * @param       Stripe\StripeObject            StripeObject
     * @return      Json
     */
    private function handleCheckoutEvent(StripeObject $stripeEvent)
    {
        // Grab the stripe object from the stripe event
        $stripeObject = $stripeEvent->data->object;
        $metadata = $stripeObject->metadata;

        // Skip events that don't match the source
        if (!$stripeEvent->livemode && $metadata["source"] != env("APP_ENV"))
        {
            Log::debug("[stripe controller] handleCheckoutEvent: skipping event because source doesn't match: " . $metadata["source"] . " != " . env("APP_ENV"));

            return response()->json([
                "status" => "skipped",
                "message" => __("webshop::validation.stripe.sources_dont_match")
            ], 200);
        }

        // Retrieve the stripe payment
        $payment = WebshopPayments::getStripePayment($stripeObject->payment_intent);
        if (!$payment)
        {
            Log::debug("[stripe controller] handleCheckoutEvent: unable to find payment information: " . $stripeObject);

            return response()->json([
                "status" => "failed",
                "message" => __("webshop::validation.stripe.payment_not_found")
            ], 399);
        }

        // Retrieve the payment method from the Stripe payment object
        $paymentMethod = $payment->payment_method_details->type;

        // Attempt to find order using client_reference_id
        $order = WebshopOrders::findOrderByUuid($stripeObject->client_reference_id);
        if (!$order)
        {
            Log::debug("[stripe controller] handleCheckoutEvent: unable to find order with id: " . $stripeObject->client_reference_id);

            return response()->json([
                "status" => "failed",
                "message" => __("webshop::validation.stripe.order_not_found")
            ], 404);
        }

        // Check if the order has been set to paid already, it should never be able to change after that
        // Once the order is paid this webhook should not be able to change data ever
        if ($order->payment_status == PaymentStatusEnum::PAID)
        {
            Log::debug("[stripe controller] handleCheckoutEvent: received duplicated webhook event from Stripe. Order: " . $order->uuid);

            return response()->json([
                "status" => "already_paid"
                "message" => __("webshop::validation.stripe.payment_already_processed")
            ], 200);
        }

        // Update order's payment status
        $order->payment_status = PaymentStatusEnum::PENDING;
        $order->payment_method = "ideal";
        $order->payment_id = $stripeObject->payment_intent;
        $order->payment_method = $paymentMethod;

        // Check if the payment has been made
        if ($stripeObject->payment_status == PaymentStatusEnum::PAID->value)
        {
            // Flag payment as paid
            $order->payment_status = PaymentStatusEnum::PAID; 
            $order->save();

            // Payment received failed event
            PaymentReceived::dispatch($order);
        }
        // The payment has failed
        else
        {
            // Revert stock decreases
            WebshopOrders::revertStockDecreases($order);

            // Flag payment as failed
            $order->payment_status = PaymentStatusEnum::FAILED;
            $order->save();

            // Dispatch payment failed event
            PaymentFailed::dispatch($order);
        }

        // Return success response
        return response()->json(["status" => "success"]);
    }

    /**
     * Handle the payment intent created event
     *
     * @param       Stripe\StripeObject            StripeObject
     * @return      Json
     */
    public function handlePaymentIntentCreated(StripeObject $stripeEvent)
    {
        // Grab the payment intent data from the stripe event
        $paymentIntent = $stripeEvent->data->object;

        // Get the Stripe session
        $session = WebshopPayments::getSession($paymentIntent->id);
        if (!$session)
        {
            return response()->json([
                "status" => "failed",
                "message" => __("webshop::validation.stripe.session_not_found")
            ], 403);
        }

        // Skip events that don't match the source
        if (!$stripeEvent->livemode && $session->metadata["source"] != env("APP_ENV"))
        {
            return response()->json([
                "status" => "skipped",
                "message" => __("webshop::validation.stripe.sources_dont_match")
            ], 200);
        }

        // Grab the order associated with the Stripe session
        $order = WebshopOrders::findOrderByUuid($session->client_reference_id);
        if (!$order)
        {
            return response()-json([
                "status" => "failed",
                "message" => __("webshop::validation.stripe.order_not_found")
            ], 404);
        }

        // Set the payment status to 'pending'
        $order->payment_status = PaymentStatusEnum::PENDING;
        $order->save();

        // Return success response
        return response()->json(["status" => "success"]);
    }

    /**
     * Handle the customer created event
     *
     * @param       Stripe\StripeObject            StripeObject
     * @return      Json
     */
    public function handleCustomerCreated(StripeObject $stripeEvent)
    {
        // TODO: save some details on the user this will be used to set some default settings for a next order
        
        // $customer = $stripeEvent->data->object;

        // $user = Users::findByEmail($customer->email);
        // if (!$user) response("Unable to find user", 404);

        // if (!Users::setPaymentDriverID($customer->id, $user))
        // {
        //     return response("Unable to save payment driver ID", 500);
        // }

        return response()->json(["status" => "success"]);
    }

    /**
     * Webhook that handles the checkout.completed event from Stripe.
     *
     * @return      \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response  status succeeded or 400 (invalid payload) or 403 (unable to find order)
     */
    public function postWebhook(StripeWebhookRequest $request)
    {
        // Attempt to parse the stripe request
        try
        {
            if (!config("webshop.payments.enable_webhook_signature_validation"))
            {
                // Create event without checking the signature
                $stripeEvent = StripeEvent::constructFrom($request->all());
            }
            else
            {
                // Check the incoming request to prevent 'replay' attacks
                // Create and validate the event
                $stripeEvent = \Stripe\Webhook::constructEvent(
                    $request->getContent(),
                    $request->header('Stripe-Signature'),
                    env("STRIPE_WEBHOOK_SECRET")
                );
            }
        }
        catch(\UnexpectedValueException $e)
        {
            Log::error("[stripe controller] postWebhook: invalid payload coming from Stripe! " . $request->ip());

            return response()->json([
                "status" => "invalid_payload",
                "message" => __("webshop::validation.stripe.invalid_payload")
            ], 400);
        }
        catch(\Stripe\Exception\SignatureVerificationException $e)
        {
            Log::error("[stripe controller] postWebhook: signature verification mismatch! " . $request->ip());

            return response()->json([
                "status" => "invalid_payload",
                "message" => __("webshop::validation.stripe.signature_mismatch")
            ], 400);
        }

        if (!$stripeEvent->livemode && env("APP_ENV") == "production")
        {
            // TODO: waarom gooien we hier een exception ipv een error zoals hierboven in alle checks?
            throw new Exception(__("webshop::validation.stripe.webhook_not_in_live_mode"));
        }

        // Handle the event
        if ($stripeEvent->type == "checkout.session.completed")
        {
            return $this->handleCheckoutEvent($stripeEvent);
        }

        if ($stripeEvent->type == "payment_intent.created")
        {
            return $this->handlePaymentIntentCreated($stripeEvent);
        }

        // TODO: if ($stripeEvent->type == "customer.created") return $this->handleCustomerCreated($stripeEvent);

        // Debug
        Log::error("[stripe controller] postWebhook: unknown or unsupported checkout event type: " . $stripeEvent->type . " ID: " . $stripeEvent->id);
        
        // Unknown or unsupported checkout event type
        return response()->json([
            "status" => "invalid_payload",
            "message" => __("webshop::validation.stripe.unknown_event_type")
        ], 400);
    }
}
