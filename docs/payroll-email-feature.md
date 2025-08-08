# Payroll Email Feature

## Overview
The payroll system now includes an email functionality that allows admin users and HR officers to send payroll statements directly to employees via email.

## Features

### Email Functionality
- **Admin/HR Access**: Only admin users and HR officers can send payroll emails
- **Professional Email Template**: Beautiful, responsive email template with detailed payroll breakdown
- **Automatic Employee Targeting**: Emails are sent directly to the employee's registered email address
- **Complete Payroll Information**: Includes all salary details, allowances, deductions, and net salary

### Email Template Features
- **Employee Information**: Name, email, pay period, date range, status, working days
- **Salary Breakdown**: 
  - Earnings (Basic salary, overtime pay)
  - Allowances (Housing, transport, meal, other)
  - Deductions (SSS, PhilHealth, Pag-IBIG, advances, other)
  - Net salary calculation
- **Professional Design**: Responsive design with proper styling
- **Status Indicators**: Color-coded status badges
- **Notes Section**: Displays any additional notes if present

## How to Use

### For Admin Users

#### From Payroll Table
1. Navigate to **Admin > Payroll** (All Payroll Records)
2. Find the payroll record you want to send
3. Click the **Email button** (envelope icon) in the Actions column
4. Confirm the action when prompted
5. The email will be sent to the employee's registered email address

#### From Payroll Details Page
1. Navigate to **Admin > Payroll** and click on a specific payroll record
2. In the **Admin Actions** section, click the **"Send Email"** button
3. Confirm the action when prompted
4. The email will be sent to the employee's registered email address

### Email Content
The email includes:
- **Subject**: "Your Payroll Statement - [Month Year - Period]"
- **Employee Details**: Name, email, pay period information
- **Complete Salary Breakdown**: All earnings, allowances, and deductions
- **Net Salary**: Final calculated amount
- **Status Information**: Current payroll status
- **Notes**: Any additional information

## Technical Implementation

### Backend Components

#### PayrollEmail Mailable Class
- **Location**: `app/Mail/PayrollEmail.php`
- **Purpose**: Handles email generation and delivery
- **Features**: 
  - Automatic subject generation
  - Employee and payroll data passing
  - Professional email template

#### Email Template
- **Location**: `resources/views/mails/PayrollEmail.blade.php`
- **Features**:
  - Responsive HTML design
  - Professional styling
  - Complete payroll information display
  - Mobile-friendly layout

#### Controller Method
- **Location**: `app/Http/Controllers/PayrollController.php`
- **Method**: `sendEmail(Payroll $payroll)`
- **Features**:
  - Admin role verification
  - Error handling
  - Success/error messages
  - Email delivery confirmation

### Frontend Components

#### Payroll Table (Index.vue)
- **Email Button**: Added to actions column for admin users
- **Icon**: Envelope icon (bx-envelope)
- **Confirmation**: User confirmation before sending
- **Success/Error Handling**: Displays appropriate messages

#### Payroll Details (Show.vue)
- **Email Button**: Added to admin actions section
- **Styling**: Green button with proper styling
- **Confirmation**: User confirmation before sending

### Routes
- **Route**: `POST /admin/payroll/{payroll}/send-email`
- **Name**: `admin.payroll.send-email`
- **Middleware**: Admin role required
- **Controller**: `PayrollController@sendEmail`

## Security Features

### Access Control
- **Role-based Access**: Only admin users can send emails
- **Middleware Protection**: Routes protected by admin middleware
- **User Verification**: Checks user role before allowing email sending

### Error Handling
- **Try-Catch Blocks**: Proper error handling for email failures
- **User Feedback**: Success and error messages displayed to users
- **Graceful Degradation**: System continues to work even if email fails

## Email Configuration

### Mail Settings
The system uses Laravel's built-in mail configuration:
- **Default Driver**: Configured in `config/mail.php`
- **Environment Variables**: Set in `.env` file
- **Supported Drivers**: SMTP, Sendmail, Log, Array, etc.

### Recommended Configuration
For production use, configure:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@dps.gov.ph
MAIL_FROM_NAME="DPS - Department of Public Safety"
```

## Testing

### Test Coverage
- **Email Sending**: Tests that emails are sent correctly
- **Employee Information**: Verifies correct employee data in emails
- **Salary Breakdown**: Ensures all salary components are included
- **Access Control**: Tests that only admins can send emails

### Running Tests
```bash
php artisan test tests/Feature/PayrollEmailTest.php
```

## Future Enhancements

### Potential Improvements
1. **Email Templates**: Multiple template options
2. **Batch Sending**: Send emails to multiple employees at once
3. **Email Tracking**: Track email delivery and read status
4. **PDF Attachments**: Attach PDF payslips to emails
5. **Scheduled Emails**: Automatically send emails on specific dates
6. **Email Preferences**: Allow employees to set email preferences

### Additional Features
1. **Email History**: Track sent emails in database
2. **Resend Functionality**: Allow resending of emails
3. **Email Templates**: Multiple template options for different purposes
4. **Notification System**: Integrate with Laravel's notification system

## Troubleshooting

### Common Issues

#### Email Not Sending
1. Check mail configuration in `.env`
2. Verify SMTP settings
3. Check server logs for errors
4. Test with log driver first

#### Permission Errors
1. Ensure user has admin role
2. Check middleware configuration
3. Verify route protection

#### Template Issues
1. Check blade template syntax
2. Verify data passing to template
3. Test template rendering

### Debug Steps
1. **Check Logs**: Review Laravel logs for errors
2. **Test Mail Driver**: Use log driver for testing
3. **Verify Data**: Ensure payroll data is complete
4. **Check Permissions**: Verify user roles and permissions

## Support

For technical support or questions about the payroll email feature, please contact the development team or refer to the system documentation.
