<?php

use Illuminate\Support\Facades\Route;
use Raw\Webshop\Http\Controllers\CheckoutController;
use Raw\Webshop\Http\Controllers\OrderController;
use Raw\Webshop\Http\Controllers\ProductController;

Route::group(["prefix" => "webshop"], function() {

    Route::get("/", function() {
        return "ewa";
    });

    Route::post("checkout", [CheckoutController::class, "postCheckout"])->name("webshop.checkout");

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

    Route::group(["prefix" => "admin"], function() {
        Route::group(["prefix" => "products"], function() {
            Route::get("create", [AdminController::class, "postCreate"])->name("webshop.admin.products.create");
            Route::get("update", [AdminController::class, "postUpdate"])->name("webshop.admin.products.update");
            Route::get("delete", [AdminController::class, "postDelete"])->name("webshop.admin.products.delete");
        });
    
    });

});