<?php

use Illuminate\Support\Facades\Route;
use Raw\Webshop\Http\Controllers\Api\AdminController;
use Raw\Webshop\Http\Controllers\Api\CheckoutController;
use Raw\Webshop\Http\Controllers\Api\DataController;
use Raw\Webshop\Http\Controllers\Api\OrderController;
use Raw\Webshop\Http\Controllers\Api\ProductController;
use Raw\Webshop\Http\Controllers\Api\StripeController;

Route::group(["prefix" => "api/webshop"], function() {

    Route::get("/", function() {
        return "webshop";
    });

    // Stripe Endpoints
    Route::group(["prefix" => "stripe"], function() {
        // Main webhook that is used to catch completed checkout events
        Route::post("endpoint", [StripeController::class, "postWebhook"])->name("stripe.webhook.endpoint");
    });

    Route::group(["prefix" => "checkout", "middleware" => ["auth:sanctum", "registration"]], function() {

        Route::get("/all", [DataController::class, "getAll"])->name("webshop.data.all");

        Route::group(["prefix" => "checkout", "middleware" => ["auth:sanctum", "registration"]], function() {
            Route::post("/", [CheckoutController::class, "postCheckout"])->name("webshop.checkout");
            Route::post("/retry", [CheckoutController::class, "postPaymentRetry"])->name("webshop.checkout.retry");
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

        Route::group(["prefix" => "admin", "middleware" => ["is_admin"]], function() {
            Route::group(["prefix" => "products"], function() {
                Route::get("create", [AdminController::class, "postCreateProduct"])->name("webshop.admin.products.create");
                Route::get("update", [AdminController::class, "postUpdateProduct"])->name("webshop.admin.products.update");
                Route::get("delete", [AdminController::class, "postDeleteProduct"])->name("webshop.admin.products.delete");
            });
            Route::group(["prefix" => "orders"], function() {
                Route::get("ship", [AdminController::class, "postShipOrder"])->name("webshop.admin.orders.ship");
                Route::get("arrive", [AdminController::class, "postArriveOrder"])->name("webshop.admin.orders.arrive");
            });
        });
    });

});