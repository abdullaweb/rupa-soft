<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_metas', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('sub_cat_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('description')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('current_qty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_metas');
    }
};
