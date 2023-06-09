<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->enum('type', ['tshirt', 'hoodie'])->default('tshirt');
            $table->string("slug");
            $table->json('title');
            $table->json('summary');
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock');
            $table->boolean('listed')->default(false);
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
        Schema::dropIfExists('products');
    }
}
