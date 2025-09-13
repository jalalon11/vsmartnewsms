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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_amount');
            $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid');
            $table->enum('status', ['draft', 'pending', 'partially_paid', 'paid', 'overdue', 'cancelled'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'balance_due']);
            $table->enum('status', ['draft', 'pending', 'paid', 'overdue', 'cancelled'])->change();
        });
    }
};
