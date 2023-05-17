<?php

return [
    "payments" => [
        "urls" => [
            "success" =>  env("STRIPE_SUCCESS_URL", "https://staging.klimbuddies.nl/"),
            "cancel" =>  env("STRIPE_CANCEL_URL", "https://staging.klimbuddies.nl/"),
        ],
        "tax_rates" => [
            "high" => env("STRIPE_HIGH_BTW", "txr_1M3fr0KnEV82zsZasVlNH3g8")
        ]
    ]
];