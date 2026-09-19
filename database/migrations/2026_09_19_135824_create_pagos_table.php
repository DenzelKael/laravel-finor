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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Temporary reference until the Subscription module is integrated.
            $table->unsignedBigInteger('subscription_id')->index();

            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50);
            $table->date('payment_date');
            $table->string('status', 30)->default('REGISTERED');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};