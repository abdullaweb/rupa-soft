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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->string('party_name')->nullable();
            $table->string('purchase_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('customer_due_id')->nullable();
            $table->string('supplier_due_id')->nullable();
            $table->string('bill_no')->nullable();
            $table->string('paid_amount')->nullable();
            $table->string('due_amount')->nullable();
            $table->string('paid_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
