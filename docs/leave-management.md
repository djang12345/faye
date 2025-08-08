# Leave Management System

## Overview

The Leave Management System provides comprehensive leave request functionality for both employees and HR officers/admins. The system includes credit-based leave allocation, approval workflows, and detailed tracking.

## Features

### 🎯 Core Features

1. **Credit-Based Leave System**
   - Employees have limited credits for each leave type
   - Cannot submit requests without sufficient credits
   - Automatic credit calculation and validation

2. **Leave Types & Credits**
   - **Emergency Leave**: 5 credits
   - **Sick Leave**: 5 credits  
   - **Vacation Leave**: 10 credits

3. **Role-Based Access**
   - **Employees**: Submit requests, view own requests and credits
   - **HR Officers/Admins**: Approve/reject requests, view all requests, add notes

4. **Request Workflow**
   - Employee submits leave request with details
   - System validates available credits
   - Admin reviews and approves/rejects with notes
   - Status tracking (pending, approved, rejected)

## Database Structure

### Leave Rules Table
```sql
leave_rules
├── id (Primary Key)
├── leave_type (emergency, sick, vacation)
├── display_name (Emergency Leave, Sick Leave, Vacation Leave)
├── default_credits (5, 5, 10)
├── description
├── is_active (boolean)
└── timestamps
```

### Leave Requests Table
```sql
leave_requests
├── id (Primary Key)
├── user_id (Foreign Key to users)
├── leave_type (emergency, sick, vacation)
├── start_date
├── end_date
├── days_requested (auto-calculated)
├── details (text)
├── status (pending, approved, rejected)
├── admin_notes (nullable)
├── approved_by (Foreign Key to users)
├── approved_at (timestamp)
└── timestamps
```

## User Interface

### 📊 Dashboard Design
- **Statistics Cards**: Total requests, pending, approved, rejected
- **Responsive Table**: Leave requests with actions
- **Modal Forms**: View details, approve/reject, check credits

### 🎨 Key Components

1. **Leave Index Page** (`/leave`)
   - Statistics overview
   - Request table with actions
   - Credit checking modal
   - Approval/rejection modals

2. **Leave Create Page** (`/leave/create`)
   - Form validation
   - Real-time credit checking
   - Date range calculation
   - Credit information display

3. **Leave Details Modal**
   - Complete request information
   - Employee details
   - Approval history
   - Admin notes

## API Endpoints

### Employee Routes
- `GET /leave` - View leave requests
- `GET /leave/create` - Create new request form
- `POST /leave` - Submit leave request
- `GET /leave/{id}` - View specific request
- `GET /leave/credits/my` - Check available credits

### Admin Routes
- `PATCH /leave/{id}/approve` - Approve/reject request

## Business Logic

### Credit Calculation
```php
// Available credits = Total credits - Used credits
$availableCredits = $rule->default_credits - $usedCredits;

// Used credits = Sum of approved requests
$usedCredits = LeaveRequest::where('user_id', $userId)
    ->where('leave_type', $leaveType)
    ->where('status', 'approved')
    ->sum('days_requested');
```

### Request Validation
1. **Credit Check**: Ensure sufficient credits available
2. **Date Validation**: Start date must be today or future
3. **Date Range**: End date must be after start date
4. **Details Required**: Minimum 10 characters

### Approval Process
1. **Admin Review**: View request details
2. **Add Notes**: Optional admin notes
3. **Decision**: Approve or reject
4. **Credit Deduction**: Only on approval

## Usage Examples

### Employee Submitting Request
1. Navigate to `/leave`
2. Click "Request Leave"
3. Select leave type (shows available credits)
4. Choose start and end dates
5. Provide detailed explanation
6. Submit request

### Admin Approving Request
1. Navigate to `/leave` (admin view)
2. Click "View" on pending request
3. Click "Approve" or "Reject"
4. Add optional notes
5. Confirm decision

### Checking Credits
1. Click "My Credits" button
2. View available credits for each leave type
3. See used and remaining credits

## Configuration

### Adding New Leave Types
1. Add record to `leave_rules` table
2. Update display names in `LeaveRequest` model
3. Rebuild frontend assets

### Modifying Credits
1. Update `default_credits` in `leave_rules` table
2. Changes apply to new requests only
3. Existing requests remain unchanged

## Security Features

- **Role-Based Access**: Employees can only see own requests
- **Credit Validation**: Server-side validation prevents over-requesting
- **Date Validation**: Prevents backdated requests
- **Approval Tracking**: Full audit trail of decisions

## Integration

The Leave Management System integrates with:
- **User Management**: Employee and admin roles
- **Dashboard**: Statistics and overview
- **Navigation**: Consistent UI/UX design
- **Database**: Proper relationships and constraints

## Future Enhancements

1. **Email Notifications**: Automatic alerts for approvals/rejections
2. **Calendar Integration**: Visual calendar for leave planning
3. **Bulk Operations**: Admin bulk approval features
4. **Leave Balance Reports**: Detailed credit usage reports
5. **Holiday Integration**: Automatic holiday detection
6. **Leave Cancellation**: Ability to cancel approved leaves
