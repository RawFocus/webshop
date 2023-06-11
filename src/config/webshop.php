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

        // Tax rates to use; see: https://github.com/RawFocus/webshop and https://stripe.com/docs/invoicing/taxes/tax-rates
        "tax_rates" => [
            // This ID corresponds to a 21% tax rate. These settings can be managed here: https://dashboard.stripe.com/test/tax-rates
            "high" => env("STRIPE_HIGH_BTW", "txr_1M3fr0KnEV82zsZasVlNH3g8")
        ],

        // Payment methods to use; see: https://github.com/RawFocus/webshop and https://stripe.com/docs/invoicing/payment-methods
        "payment_method_types" => [explode(",", env("STRIPE_PAYMENT_METHODS", "ideal"))],

        // Enable webhook signature validation; see: https://github.com/RawFocus/webshop for more information
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
            "auth" => ["auth:sanctum", "registration"], // TODO: remove registration from package to make the package more agnostic

            // Product route middlewares
            "products" => ["is_admin"], // TODO: remove is_admin from package to make the package more agnostic

            // Order route middlewares
            "orders" => ["is_admin"], // TODO: remove is_admin from package to make the package more agnostic

            // Checkout route middelewares
            "checkout" => [],

        ],

    ],

];
