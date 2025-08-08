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
        Schema::create('leave_rules', function (Blueprint $table) {
            $table->id();
            $table->string('leave_type'); // emergency, sick, vacation
            $table->string('display_name'); // Emergency Leave, Sick Leave, Vacation Leave
            $table->integer('default_credits'); // 5, 5, 10
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_rules');
    }
};
