<?php

use Illuminate\Support\Facades\Route;
use Raw\Webshop\Http\Controllers\Api\AdminController;
use Raw\Webshop\Http\Controllers\Api\CheckoutController;
use Raw\Webshop\Http\Controllers\Api\DataController;
use Raw\Webshop\Http\Controllers\Api\OrderController;
use Raw\Webshop\Http\Controllers\Api\ProductController;
use Raw\Webshop\Http\Controllers\Api\StripeController;

// Middleware settings
$authMiddleware = config("webshop.middleware.auth");
$checkoutMiddleware = config("webshop.middleware.checkout");
$productMiddleware = config("webshop.middleware.products");
$orderMiddleware = config("webshop.middleware.orders");

// Webshop routes
Route::group(["prefix" => "api/webshop"], function() {

    // Data retrieval 
    Route::get("data", [DataController::class, "getAll"])->name("webshop.data");

    // Authenticated only endpoints
    Route::group(["middleware" => $authMiddleware], function() {

        // Checkout endpoints
        Route::group(["prefix" => "checkout", "middleware" => $checkoutMiddleware], function() {
            Route::post("/", [CheckoutController::class, "postCheckout"])->name("webshop.checkout");
            Route::post("/retry", [CheckoutController::class, "postPaymentRetry"])->name("webshop.checkout.retry");
        });

        // Product endpoints
        Route::group(["prefix" => "products"], function() {
            // Getters
            Route::get("/", [ProductController::class, "getAll"])->name("webshop.products.all");
            Route::get("find-by-id/{id}", [ProductController::class, "getFindById"])->name("webshop.products.find-by-id");
            Route::get("find-by-slug/{slug}", [ProductController::class, "getFindBySlug"])->name("webshop.products.find-by-slug");
            // Setters
            Route::group(["middleware" => $productMiddleware], function() {
                Route::post("create", [ProductController::class, "postCreate"])->name("webshop.products.create");
                Route::post("update", [ProductController::class, "postUpdate"])->name("webshop.products.update");
                Route::post("delete", [ProductController::class, "postDelete"])->name("webshop.products.delete");
            });
        });

        // Order endpoints
        Route::group(["prefix" => "orders"], function() {
            // Getters
            Route::get("/", [OrderController::class, "getAll"])->name("webshop.orders.all");
            Route::get("find-by-id/{id}", [OrderController::class, "getFindById"])->name("webshop.orders.find-by-id");
            Route::get("find-by-uuid/{uuid}", [OrderController::class, "getFindByUuid"])->name("webshop.orders.find-by-uuid");
            // Setters
            Route::group(["middleware" => $orderMiddleware], function() {
                Route::post("flag-as-shipped", [OrderController::class, "postFlagAsShipped"])->name("webshop.orders.flag-as-shipped");
                Route::post("flag-as-arrived", [OrderController::class, "postFlagAsArrived"])->name("webshop.orders.flag-as-arrived");
            });
        });
        
    });

    // Stripe Endpoints
    Route::group(["prefix" => "stripe"], function() {

        // Main webhook that is used to catch completed checkout events
        Route::post("endpoint", [StripeController::class, "postWebhook"])->name("stripe.webhook.endpoint");

    });
    
});