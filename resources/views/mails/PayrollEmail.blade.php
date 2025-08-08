<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Statement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .employee-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .employee-info h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .salary-breakdown {
            margin: 25px 0;
        }

        .breakdown-section {
            margin-bottom: 20px;
        }

        .breakdown-section h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 16px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .breakdown-item:last-child {
            border-bottom: none;
        }

        .breakdown-item.total {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }

        .net-salary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 6px;
            text-align: center;
            margin-top: 25px;
        }

        .net-salary h3 {
            margin: 0 0 10px 0;
            font-size: 20px;
        }

        .net-salary .amount {
            font-size: 32px;
            font-weight: bold;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-draft { background-color: #6c757d; color: white; }
        .status-pending { background-color: #ffc107; color: #212529; }
        .status-approved { background-color: #28a745; color: white; }
        .status-paid { background-color: #007bff; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }

        @media (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .container {
                margin: 10px;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Payroll Statement</h1>
            <p>{{ $payroll->period_display }}</p>
        </div>

        <div class="content">
            <div class="employee-info">
                <h3>Employee Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Employee Name:</span>
                        <span class="info-value">{{ $employee->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $employee->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Pay Period:</span>
                        <span class="info-value">{{ $payroll->period_display }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date Range:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($payroll->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($payroll->end_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $payroll->status }}">{{ ucfirst($payroll->status) }}</span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Working Days:</span>
                        <span class="info-value">{{ $payroll->total_days_worked }} days</span>
                    </div>
                </div>
            </div>

            <div class="salary-breakdown">
                <div class="breakdown-section">
                    <h4>Earnings</h4>
                    <div class="breakdown-item">
                        <span>Basic Salary (Half Month)</span>
                        <span>₱{{ number_format($payroll->gross_salary, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Overtime Pay ({{ $payroll->overtime_hours }} hours)</span>
                        <span>₱{{ number_format($payroll->overtime_pay, 2) }}</span>
                    </div>
                    <div class="breakdown-item total">
                        <span>Total Earnings</span>
                        <span>₱{{ number_format($payroll->gross_salary + $payroll->overtime_pay, 2) }}</span>
                    </div>
                </div>

                <div class="breakdown-section">
                    <h4>Allowances</h4>
                    <div class="breakdown-item">
                        <span>Housing Allowance</span>
                        <span>₱{{ number_format($payroll->housing_allowance, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Transport Allowance</span>
                        <span>₱{{ number_format($payroll->transport_allowance, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Meal Allowance</span>
                        <span>₱{{ number_format($payroll->meal_allowance, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Other Allowance</span>
                        <span>₱{{ number_format($payroll->other_allowance, 2) }}</span>
                    </div>
                    <div class="breakdown-item total">
                        <span>Total Allowances</span>
                        <span>₱{{ number_format($payroll->total_allowances, 2) }}</span>
                    </div>
                </div>

                <div class="breakdown-section">
                    <h4>Deductions</h4>
                    <div class="breakdown-item">
                        <span>SSS Contribution</span>
                        <span>₱{{ number_format($payroll->sss_deduction, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>PhilHealth Contribution</span>
                        <span>₱{{ number_format($payroll->philhealth_deduction, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Pag-IBIG Contribution</span>
                        <span>₱{{ number_format($payroll->pagibig_deduction, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Salary Advance</span>
                        <span>₱{{ number_format($payroll->advance_deduction, 2) }}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Other Deductions</span>
                        <span>₱{{ number_format($payroll->other_deduction, 2) }}</span>
                    </div>
                    <div class="breakdown-item total">
                        <span>Total Deductions</span>
                        <span>₱{{ number_format($payroll->total_deductions, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="net-salary">
                <h3>Net Salary</h3>
                <div class="amount">₱{{ number_format($payroll->net_salary, 2) }}</div>
            </div>

            @if($payroll->notes)
            <div style="margin-top: 25px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px;">
                <h4 style="margin: 0 0 10px 0; color: #856404;">Notes</h4>
                <p style="margin: 0; color: #856404;">{{ $payroll->notes }}</p>
            </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>DPS</strong></p>
            <p>This is an automated payroll statement. Please contact HR if you have any questions.</p>
            <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>

</html>
