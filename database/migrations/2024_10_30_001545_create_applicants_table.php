<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('mother_firstname')->nullable();
            $table->string('mother_middlename')->nullable();
            $table->string('mother_lastname')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('father_firstname')->nullable();
            $table->string('father_middlename')->nullable();
            $table->string('father_lastname')->nullable();
            $table->string('father_occupation')->nullable();
            $table->timestamps();
        });

        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('email');
            $table->string('age');
            $table->enum('sex', [
                'Male',
                'Female'
            ]);
            $table->string('height');
            $table->string('weight');
            $table->date('birthdate');
            $table->string('status');
            $table->string('citizenship');
            $table->string('barangay');
            $table->string('municipality');
            $table->string('province');
            $table->foreignId('parents_id')->nullable()->constrained('parents')->onDelete('cascade');
            $table->string('country');
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade');
            $table->enum('status', ['PENDING', 'ACCEPTED', 'REJECTED']);
            $table->date('interview_date')->nullable();
            $table->time('interview_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
        Schema::dropIfExists('applicants');
        Schema::dropIfExists('applications');
    }
};
