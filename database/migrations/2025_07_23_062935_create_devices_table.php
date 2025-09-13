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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_type_id')->constrained()->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->string('serial_number')->nullable();
            $table->string('imei')->nullable(); // For phones
            $table->year('year')->nullable();
            $table->string('color')->nullable();
            $table->text('specifications')->nullable();
            $table->text('accessories')->nullable(); // Charger, case, etc.
            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
