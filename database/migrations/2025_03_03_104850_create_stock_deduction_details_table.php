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
        Schema::create('stock_deduction_details', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('deduction_id')->nullable();
            $table->string('category_id')->nullable();
            $table->string('sub_cat_id')->nullable();
            $table->longText('description')->nullable();
            $table->string('quantity')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('total_price')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_deduction_details');
    }
};
