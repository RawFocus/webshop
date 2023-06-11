<?php

return [

    /**
     * Payments
     * -----------------------------------------------------------------------------
     * Configure Stripe payments here.
     */
    "payments" => [

        // Endpoints stripe should use for success and cancel redirects
        "urls" => [
            "success" => env("STRIPE_SUCCESS_URL", "https://staging.klimbuddies.nl/"),
            "cancel" => env("STRIPE_CANCEL_URL", "https://staging.klimbuddies.nl/"),
        ],

        "tax_rates" => [
            // This Tax Object ID corresponds to a 21% tax rate. These objects can be managed here: https://dashboard.stripe.com/tax-rates
            // Read the Stripe docs here: https://stripe.com/docs/invoicing/taxes/tax-rates
            "high" => env("STRIPE_HIGH_BTW", "txr_1M3fr0KnEV82zsZasVlNH3g8")
        ],

        // Payment methods to use
        // See: https://stripe.com/docs/invoicing/payment-methods
        "payment_method_types" => [explode(",", env("STRIPE_PAYMENT_METHODS", "ideal"))],

        // Enable webhook signature validation
        // Signature validation prevents attackers from triggering webhooks for your account.
        // see also https://stripe.com/docs/webhooks/signatures
        "enable_webhook_signature_validation" => env("STRIPE_ENABLE_WEBHOOK_SIGNATURE_VALIDATION", false),

    ],

    /**
     * Routing
     * -----------------------------------------------------------------------------
     * Configure the middleware that should be used for the webshop routes.
     */
    "routing" => [

        "middleware" => [

            // Authenticated only route middlewares
            "auth" => ["auth:sanctum", "registration"],

            // Product route middlewares
            "products" => ["is_admin"],

            // Order route middlewares
            "orders" => ["is_admin"],

            // Checkout route middelewares
            "checkout" => [],

        ],

    ],

];
