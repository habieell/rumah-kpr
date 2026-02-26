<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mortgage_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interest_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('duration'); // years
            $table->string('bank_name');
            $table->unsignedInteger('interest'); // %
            $table->unsignedBigInteger('house_price');
            $table->unsignedInteger('dp_percentage'); // %
            $table->unsignedBigInteger('dp_total_amount');
            $table->unsignedBigInteger('loan_total_amount');
            $table->unsignedBigInteger('monthly_amount');
            $table->unsignedBigInteger('loan_interest_total_amount');

            $table->string('status');
            $table->string('documents');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mortgage_requests');
    }
};
