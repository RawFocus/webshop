<?php

namespace Raw\Webshop\Http\Controllers\Stripe;

use Log;
use Users;
use Webshop;
use Orders;
use Payments;
use Exception;

use App\Http\Controllers\Controller;
use App\Enums\PaymentStatusEnum;
use App\Http\Requests\Api\Stripe\StripeWebhookRequest;

use Stripe\Stripe;
use Stripe\StripeObject;
use Stripe\Event as StripeEvent;

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
            return response()->json([
                "status" => "skipped"
            ]);
        }

        $payment = Payments::getPayment($stripeObject->payment_intent);
        if (!$payment) {
            Log::debug("StripeController::handleCheckoutEvent: unable to find payment information: " . $stripeObject);
            return response("Unable to find payment information", 399);
        }

        $paymentMethod = $payment->payment_method_details->type;

        // Attempt to find order using client_reference_id
        $order = Webshop::getOrderByUuid($stripeObject->client_reference_id);
        if (!$order)
        {
            Log::debug("StripeController::handleCheckoutEvent: Unable to find order with id: " . $stripeObject->client_reference_id);
            return response("Unable to find order", 404);
        }

        // Check if the order has been set to paid already, it should never be able to change after that
        // Once the order is paid this webhook should not be able to change data ever
        if ($order->payment_status == PaymentStatusEnum::PAID)
        {
            Log::debug("Received duplicated webhook event from Stripe. Order: " . $order->uuid);
            return response()->json(["status" => "already_paid"]);
        }

        // Update order's payment status
        $order->payment_status = PaymentStatusEnum::PAID;
        $order->payment_method = "ideal";
        $order->payment_id = $stripeObject->payment_intent;
        $order->payment_method = $paymentMethod;
        $order->save();

        // Check if the payment has been made
        if ($stripeObject->payment_status == PaymentStatusEnum::PAID)
        {
            // Flag order as paid
            $order->payment_status = PaymentStatusEnum::PAID;

            // TODO: Send payment received email
            // event(new PaymentReceived($order));
        }
        // The payment has failed (or is pending or whatever)
        else
        {
            $order = Webshop::revertStockDecreases($order);
        }

        $order->save();

        // Return success response
        return response()->json(["status" => "succeeded"]);
    }

    /**
     * Handle the payment intent created event
     *
     * @param       Stripe\StripeObject            StripeObject
     * @return      Json
     */
    public function handlePaymentIntentCreated(StripeObject $stripeEvent)
    {
        $paymentIntent = $stripeEvent->data->object;

        // Get original stripe session from Stripe
        $sessions = Payments::getSessions($paymentIntent->id);
        if ($sessions->count() == 0) return response("Unable to find session", 403);

        // Get the first session
        $session = $sessions[0];

        // Skip events that don't match the source
        if (!$stripeEvent->livemode && $session->metadata["source"] != env("APP_ENV"))
        {
            return response()->json([
                "status" => "skipped"
            ]);
        }

        $order = Orders::findByUuid($session->client_reference_id);
        if (!$order)
        {
            return response("Unable to find order", 404);
        }

        // Set the payment status to 'pending'
        $order->payment_status = PaymentStatusEnum::PENDING;
        $order->save();

        return response()->json([
            "status" => "succeeded"
        ]);
    }

    /**
     * Handle the customer created event
     *
     * @param       Stripe\StripeObject            StripeObject
     * @return      Json
     */
    public function handleCustomerCreated(StripeObject $stripeEvent)
    {
        $customer = $stripeEvent->data->object;
        $user = Users::findByEmail($customer->email);
        if (!$user) response("Unable to find user", 404);

        if (!Users::setPaymentDriverID($customer->id, $user))
        {
            return response("Unable to save payment driver ID", 500);
        }

        return response()->json([
            "status" => "succeeded"
        ]);
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
            if (!config("payments.enable_webhook_signature_validation"))
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
            // Invalid payload
            Log::error("Invalid payload coming from Stripe! " . $request->ip());
            return response('Invalid payload', 400);
        }
        catch(\Stripe\Exception\SignatureVerificationException $e)
        {
            // Invalid payload
            Log::error("Signature verification mismatch! " . $request->ip());
            return response('Invalid payload', 400);
        }

        if (!$stripeEvent->livemode && env("APP_ENV") == "production")
        {
            throw new Exception("This webhook request has livemode set to false");
        }

        // Handle the event
        if ($stripeEvent->type == "checkout.session.completed") return $this->handleCheckoutEvent($stripeEvent);
        if ($stripeEvent->type == "payment_intent.created") return $this->handlePaymentIntentCreated($stripeEvent);
        // TODO: if ($stripeEvent->type == "customer.created") return $this->handleCustomerCreated($stripeEvent);

        // Unknown or unsupported checkout event type
        Log::error("Unknown or unsupported checkout event type: " . $stripeEvent->type . " ID: " . $stripeEvent->id);
        return response("Invalid payload", 400);
    }
}
