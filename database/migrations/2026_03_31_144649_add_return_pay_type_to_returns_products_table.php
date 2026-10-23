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
        Schema::table('returns_products', function (Blueprint $table) {
            $table->enum('return_pay_type', ['cash', 'cheque', 'other'])
                ->default('cash')
                ->after('notes')
                ->comment('How the refund was given to the customer');

            $table->boolean('cheque_cancelled')
                ->default(false)
                ->after('return_pay_type')
                ->comment('Whether the original sale cheque was cancelled/returned due to full invoice return');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returns_products', function (Blueprint $table) {
            $table->dropColumn(['return_pay_type', 'cheque_cancelled']);
        });
    }
};
