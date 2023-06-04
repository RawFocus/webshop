<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->unsignedInteger("user_id");
            $table->string('name');
            $table->string('email');
            $table->string('street');
            $table->string('postal_code');
            $table->string('city');
            $table->string('country');
            $table->string('payment_method')->nullable();
            $table->string('payment_id')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'pending'])->default('unpaid');
            $table->enum('order_status', ['open', 'fulfilled', 'shipped', 'arrived', 'refunded'])->default('open');
            $table->unsignedInteger('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
