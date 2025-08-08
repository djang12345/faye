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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Pay Period
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('month');
            $table->integer('year');
            $table->enum('period', ['1st Half', '2nd Half']);
            
            // Basic salary information
            $table->decimal('basic_salary', 10, 2);
            
            // Attendance-based calculations
            $table->integer('total_days_worked')->default(0);
            $table->decimal('total_hours_worked', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('absent_days')->default(0);
            
            // Earnings
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            
            // Allowances
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('meal_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('total_allowances', 10, 2)->default(0);
            
            // Deductions (No tax - as per agency requirement)
            $table->decimal('sss_deduction', 10, 2)->default(0);
            $table->decimal('philhealth_deduction', 10, 2)->default(0);
            $table->decimal('pagibig_deduction', 10, 2)->default(0);
            $table->decimal('advance_deduction', 10, 2)->default(0);
            $table->decimal('other_deduction', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            
            $table->decimal('net_salary', 10, 2);
            
            $table->enum('status', ['draft', 'pending', 'approved', 'paid', 'cancelled'])->default('draft');
            
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('payment_date')->nullable();
            
            $table->boolean('payslip_generated')->default(false);
            $table->string('payslip_url')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'start_date', 'end_date', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
