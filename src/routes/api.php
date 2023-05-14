<?php

use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "webshop"], function() {

    Route::get("/", function() {
        return "ewa";
    });

});