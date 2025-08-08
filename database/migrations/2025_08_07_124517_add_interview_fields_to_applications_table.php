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
            $table->string('interview_location')->nullable()->after('interview_time');
            $table->text('interview_notes')->nullable()->after('interview_location');
            $table->enum('interview_result', ['PASSED', 'FAILED', 'PENDING'])->nullable()->after('interview_notes');
            $table->integer('interview_score')->nullable()->after('interview_result');
            $table->string('interviewer_name')->nullable()->after('interview_score');
            $table->timestamp('interview_conducted_at')->nullable()->after('interviewer_name');
            $table->text('rejection_reason')->nullable()->after('interview_conducted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'interview_location',
                'interview_notes',
                'interview_result',
                'interview_score',
                'interviewer_name',
                'interview_conducted_at',
                'rejection_reason',
            ]);
        });
    }
};
