<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // kalau house_price sudah BIGINT tapi gak apa-apa di-set lagi
        DB::statement("ALTER TABLE mortgage_requests MODIFY house_price BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY dp_total_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY loan_total_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY monthly_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY loan_interest_total_amount BIGINT UNSIGNED NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE mortgage_requests MODIFY house_price BIGINT NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY dp_total_amount INT NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY loan_total_amount INT NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY monthly_amount INT NOT NULL");
        DB::statement("ALTER TABLE mortgage_requests MODIFY loan_interest_total_amount INT NOT NULL");
    }
};
