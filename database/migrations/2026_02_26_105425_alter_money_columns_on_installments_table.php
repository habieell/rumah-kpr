<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE installments MODIFY sub_total_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY total_tax_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY insurance_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY grand_total_amount BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY remaining_loan_amount BIGINT UNSIGNED NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE installments MODIFY sub_total_amount INT NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY total_tax_amount INT NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY insurance_amount INT NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY grand_total_amount INT NOT NULL");
        DB::statement("ALTER TABLE installments MODIFY remaining_loan_amount INT NOT NULL");
    }
};
