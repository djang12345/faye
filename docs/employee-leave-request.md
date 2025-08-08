# Employee Leave Request System

## Overview

The Employee Leave Request System provides a user-friendly interface for employees to submit leave requests with appropriate restrictions and validation. The system follows the same design pattern as the admin leave management but with employee-specific limitations.

## Features

### 🎯 Core Features

1. **Employee-Specific Restrictions**
   - Employees can only submit leave requests for themselves
   - Cannot edit or delete approved/rejected requests
   - Can only edit pending requests
   - Cannot approve/reject requests (admin only)

2. **Credit-Based System**
   - Real-time credit checking
   - Visual credit information display
   - Automatic validation before submission
   - Clear insufficient credit warnings

3. **Professional Design**
   - Follows admin design pattern
   - Responsive layout
   - Clear visual feedback
   - User-friendly interface

## User Interface

### 📊 Leave Request Form Design

#### Header Section
- **Title**: "Request Leave"
- **Back Button**: Returns to "My Leave Requests"
- **Professional Layout**: Clean, organized design

#### Form Sections

1. **Leave Type and Employee Info**
   - **Leave Type Dropdown**: Shows available credits for each type
   - **Employee Display**: Shows current user info (read-only)
   - **Credit Information**: Real-time credit display

2. **Credit Information Display**
   - **Total Credits**: Shows maximum available credits
   - **Available Credits**: Shows current available credits
   - **Days Requesting**: Shows calculated days for current request
   - **Warning System**: Clear insufficient credit alerts

3. **Date Range Selection**
   - **Start Date**: Required field with validation
   - **End Date**: Required field with validation
   - **Automatic Calculation**: Days requested calculated automatically

4. **Request Details**
   - **Large Text Area**: 6 rows for detailed explanation
   - **Placeholder Text**: Guides user on what to include
   - **Character Minimum**: 10 characters required
   - **Validation**: Real-time character count

5. **Important Notice Section**
   - **Policy Information**: Clear guidelines for employees
   - **Advance Notice Requirements**: 3 days for vacation leave
   - **Approval Process**: Subject to HR/Admin approval
   - **Edit Restrictions**: Cannot edit approved/rejected requests

6. **Action Buttons**
   - **Cancel Button**: Returns to leave index
   - **Submit Button**: Disabled if insufficient credits
   - **Loading States**: Spinner during submission

## Business Rules

### Employee Restrictions

1. **Request Creation**
   - Can only create requests for themselves
   - Must have sufficient credits
   - Must provide detailed explanation (min 10 chars)
   - Cannot backdate requests

2. **Request Editing**
   - Can only edit pending requests
   - Cannot edit approved requests
   - Cannot edit rejected requests
   - Cannot change request status

3. **Request Deletion**
   - Can only delete pending requests
   - Cannot delete approved requests
   - Cannot delete rejected requests

### Validation Rules

1. **Credit Validation**
   - Must have sufficient credits for requested days
   - Real-time credit checking
   - Clear error messages for insufficient credits

2. **Date Validation**
   - Start date must be today or future
   - End date must be after start date
   - Automatic day calculation

3. **Content Validation**
   - Minimum 10 characters for details
   - Required fields validation
   - Clear error messages

## Technical Implementation

### Frontend Components

#### Leave/Create.vue
- **Responsive Design**: Bootstrap-based layout
- **Real-time Validation**: Computed properties for validation
- **Credit Display**: Visual credit information cards
- **Form Handling**: Inertia.js form management
- **Error Handling**: Comprehensive error display

#### Key Features
```vue
<!-- Credit Information Display -->
<div v-if="selectedLeaveRule" class="mb-4 p-3 bg-info bg-opacity-10 rounded">
    <h6 class="text-info mb-3">
        <i class="bx bx-info-circle me-1"></i>
        Leave Credit Information
    </h6>
    <!-- Credit cards with total, available, and requesting -->
</div>

<!-- Important Notice -->
<div class="alert alert-info">
    <i class="bx bx-info-circle me-2"></i>
    <strong>Important:</strong> 
    <ul class="mb-0 mt-2">
        <li>Leave requests must be submitted at least 3 days in advance for vacation leave</li>
        <li>Emergency and sick leave can be submitted with shorter notice</li>
        <li>All requests are subject to approval by HR/Admin</li>
        <li>You cannot edit or cancel requests once they are approved or rejected</li>
    </ul>
</div>
```

### Backend Validation

#### LeaveController.php
```php
// Employee-specific restrictions
if (!$user->isAdmin() && $leaveRequest->status !== 'pending') {
    return back()->withErrors(['error' => 'You cannot edit leave requests that have been approved or rejected.']);
}

// Credit validation
$availableCredits = $leaveRule->getAvailableCredits($user->id);
if ($availableCredits < $daysRequested) {
    return back()->withErrors(['days_requested' => "You only have {$availableCredits} credits available..."]);
}
```

## User Experience

### 🎨 Design Features

1. **Visual Hierarchy**
   - Clear section separation
   - Consistent color coding
   - Professional typography

2. **Interactive Elements**
   - Real-time credit updates
   - Dynamic form validation
   - Responsive button states

3. **Information Display**
   - Credit information cards
   - Status indicators
   - Warning messages

### 📱 Responsive Design

- **Mobile Friendly**: Works on all screen sizes
- **Touch Optimized**: Large buttons and inputs
- **Readable Text**: Appropriate font sizes
- **Clear Layout**: Organized information display

## Error Handling

### Validation Errors
- **Credit Insufficient**: Clear warning with available credits
- **Date Validation**: Specific error messages for date issues
- **Content Validation**: Minimum character requirements
- **Required Fields**: Clear indication of missing fields

### User Feedback
- **Success Messages**: Confirmation of successful submission
- **Error Messages**: Clear explanation of issues
- **Loading States**: Visual feedback during processing
- **Confirmation Dialogs**: For destructive actions

## Security Features

### Access Control
- **Role-based Access**: Only employees can access their own requests
- **Data Validation**: Server-side validation for all inputs
- **CSRF Protection**: Built-in Laravel CSRF protection
- **Input Sanitization**: Proper data sanitization

### Data Integrity
- **Credit Validation**: Server-side credit checking
- **Status Validation**: Proper status transition rules
- **Date Validation**: Server-side date validation
- **Content Validation**: Minimum content requirements

## Usage Guidelines

### For Employees

1. **Submitting a Request**
   - Navigate to Leave Management
   - Click "Request Leave"
   - Select leave type (check available credits)
   - Choose start and end dates
   - Provide detailed explanation
   - Submit request

2. **Managing Requests**
   - View all your requests in the table
   - Edit pending requests only
   - Delete pending requests only
   - Check credit status anytime

3. **Best Practices**
   - Submit vacation requests 3+ days in advance
   - Provide detailed explanations
   - Check available credits before submitting
   - Review request details carefully

### Error Prevention

1. **Credit Management**
   - Check available credits before submitting
   - Consider shorter leave periods if credits are low
   - Plan leave requests in advance

2. **Date Selection**
   - Ensure start date is today or future
   - End date must be after start date
   - Consider work schedule when planning

3. **Content Quality**
   - Provide detailed explanations
   - Include relevant context
   - Explain work coverage during absence

## Future Enhancements

### Potential Improvements

1. **Enhanced Validation**
   - Holiday calendar integration
   - Work schedule validation
   - Conflict detection with other requests

2. **User Experience**
   - Draft saving functionality
   - Request templates
   - Bulk request submission

3. **Integration Features**
   - Calendar integration
   - Email notifications
   - Mobile app support

4. **Advanced Features**
   - Request templates
   - Auto-approval for certain types
   - Credit rollover system
   - Leave balance forecasting

## Support and Troubleshooting

### Common Issues

1. **Insufficient Credits**
   - Check available credits before submitting
   - Consider shorter leave periods
   - Contact HR for credit adjustments

2. **Date Validation Errors**
   - Ensure start date is not in the past
   - End date must be after start date
   - Check for weekend/holiday conflicts

3. **Form Submission Errors**
   - Ensure all required fields are filled
   - Provide minimum 10 characters in details
   - Check internet connection

### Getting Help

- **System Issues**: Contact IT support
- **Policy Questions**: Contact HR department
- **Credit Issues**: Contact HR for adjustments
- **Technical Problems**: Check system status page

## Conclusion

The Employee Leave Request System provides a comprehensive, user-friendly interface for employees to manage their leave requests while maintaining appropriate restrictions and validation. The system ensures data integrity, provides clear feedback, and follows professional design standards while respecting employee limitations and business rules.
