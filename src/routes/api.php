<?php

use Illuminate\Support\Facades\Route;

use Raw\Webshop\Http\Controllers\OrderController;
use Raw\Webshop\Http\Controllers\ProductController;

Route::group(["prefix" => "webshop"], function() {

    Route::get("/", function() {
        return "ewa";
    });

    Route::group(["prefix" => "checkout"], function() {
        Route::get("/", [ProductController::class, "getAll"])->name("webshop.products.all");
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

    Route::group(["prefix" => "admin"], function() {
        Route::get("/", [OrderController::class, "getAll"])->name("webshop.orders.all");
        Route::get("find-by-id/{id}", [OrderController::class, "getFindById"])->name("webshop.orders.find-by-id");
        Route::get("find-by-uuid/{uuid}", [OrderController::class, "getFindByUuid"])->name("webshop.orders.find-by-uuid");
    });

});