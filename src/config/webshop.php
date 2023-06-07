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
            "success" => env("STRIPE_SUCCESS_URL", ""),
            "cancel" => env("STRIPE_CANCEL_URL", ""),
        ],

        // Tax rates to use; see: [link here] for more information
        "tax_rates" => [
            "high" => env("STRIPE_HIGH_BTW", "txr_1M3fr0KnEV82zsZasVlNH3g8")
        ],

        // Payment methods to use; see: [link here] for more information
        "payment_method_types" => [explode(",", env("STRIPE_PAYMENT_METHODS", "ideal"))],

        // Enable webhook signature validation; see: [link here] for more information
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
            "auth" => ["auth:sanctum"],

            // Product route middlewares
            "products" => [],

            // Order route middlewares
            "orders" => [],

            // Checkout route middelewares
            "checkout" => [],

        ],

    ],

];
