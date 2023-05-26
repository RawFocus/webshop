<?php

use Illuminate\Support\Facades\Route;
use Raw\Webshop\Http\Controllers\CheckoutController;
use Raw\Webshop\Http\Controllers\DataController;
use Raw\Webshop\Http\Controllers\OrderController;
use Raw\Webshop\Http\Controllers\ProductController;
use Raw\Webshop\Http\Controllers\StripeController;

Route::group(["prefix" => "api/webshop"], function() {

    Route::get("/", function() {
        return "webshop";
    });

    Route::get("/all", [DataController::class, "getAll"])->name("webshop.data.all");

    Route::group(["prefix" => "checkout"], function() {
        Route::post("/", [CheckoutController::class, "postCheckout"])->name("webshop.checkout");
    });

    // Stripe Endpoints
    Route::group(["prefix" => "stripe"], function() {
        // Main webhook that is used to catch completed checkout events
        Route::post("endpoint", [StripeController::class, "postWebhook"])->name("stripe.webhook.endpoint");
    });

    Route::group(["prefix" => "products"], function() {
        Route::get("/", [ProductController::class, "getAll"])->name("webshop.products.all");
        Route::get("find-by-id/{id}", [ProductController::class, "getFindById"])->name("webshop.products.find-by-id");
        Route::get("find-by-slug/{slug}", [ProductController::class, "getFindBySlug"])->name("webshop.products.find-by-slug");
    });

    Route::group(["prefix" => "orders"], function() {
        Route::get("/", [OrderController::class, "getAll"])->name("webshop.orders.all");
        Route::get("find-by-id/{id}", [OrderController::class, "getFindById"])->name("webshop.orders.find-by-id");
        Route::get("find-by-uuid/{uuid}", [OrderController::class, "getFindByUuid"])->name("webshop.orders.find-by-uuid");
    });

});