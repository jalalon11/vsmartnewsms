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
        Schema::table('repair_orders', function (Blueprint $table) {
            // Remove the single service_id foreign key constraint
            $table->dropForeign(['service_id']);
            
            // Keep the service_id column for backward compatibility but make it nullable
            // We'll use this as the primary service for display purposes
            $table->foreignId('service_id')->nullable()->change();
            
            // Add foreign key back with nullable constraint
            $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
            
            // Add total estimated duration field (sum of all services)
            $table->integer('total_estimated_duration')->nullable()->after('estimated_completion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_orders', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['service_id']);
            
            // Make service_id required again
            $table->foreignId('service_id')->nullable(false)->change();
            
            // Add foreign key back with cascade delete
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            
            // Remove the total estimated duration field
            $table->dropColumn('total_estimated_duration');
        });
    }
};
