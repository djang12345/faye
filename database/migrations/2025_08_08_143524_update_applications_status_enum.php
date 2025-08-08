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
        Schema::table('applications', function (Blueprint $table) {
            // Drop the existing enum and recreate it with all values
            $table->enum('status', ['PENDING', 'INTERVIEW_SCHEDULED', 'INTERVIEWED', 'ACCEPTED', 'REJECTED'])->default('PENDING')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED'])->default('PENDING')->change();
        });
    }
};
