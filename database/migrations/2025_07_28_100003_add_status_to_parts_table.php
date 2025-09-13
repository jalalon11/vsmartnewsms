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
        Schema::table('parts', function (Blueprint $table) {
            $table->enum('status', ['ordered', 'in_transit', 'in_stock'])->default('in_stock')->after('is_active');
            $table->date('order_date')->nullable()->after('status');
            $table->date('expected_arrival_date')->nullable()->after('order_date');
            $table->date('received_date')->nullable()->after('expected_arrival_date');
            $table->text('status_notes')->nullable()->after('received_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropColumn(['status', 'order_date', 'expected_arrival_date', 'received_date', 'status_notes']);
        });
    }
};
