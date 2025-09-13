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
        Schema::table('technicians', function (Blueprint $table) {
            $table->date('hire_date')->nullable()->after('hourly_rate');
            $table->string('phone')->nullable()->after('employee_id');
            $table->text('certifications')->nullable()->after('skills');
            $table->text('notes')->nullable()->after('certifications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            $table->dropColumn('hire_date');
            $table->dropColumn('phone');
            $table->dropColumn('certifications');
            $table->dropColumn('notes');
        });
    }
};
