# Automated Startup with Sample Data

## Overview

The DPS system includes an automated startup feature that automatically loads sample data when the database is empty. This ensures that new installations or fresh databases have realistic data to work with immediately.

## How It Works

### 1. Service Provider Detection

The `SampleDataServiceProvider` automatically runs when the application starts and checks if the database is empty:

```php
// Only run in local environment and when database is empty
if (app()->environment('local') && $this->shouldLoadSampleData()) {
    $this->loadSampleData();
}
```

### 2. Database Check

The system checks if the database has any data by looking at key tables:

```php
private function shouldLoadSampleData(): bool
{
    try {
        // Check if database has any data
        return User::count() === 0 && LeaveRule::count() === 0;
    } catch (\Exception $e) {
        // If there's an error (like database not set up), don't load sample data
        return false;
    }
}
```

### 3. Sample Data Loading

If the database is empty, the system automatically runs the `dps:load-sample-data` command:

```php
private function loadSampleData(): void
{
    try {
        // Run the sample data command
        Artisan::call('dps:load-sample-data');
        
        // Log that sample data was loaded
        \Log::info('Sample data automatically loaded on application startup');
    } catch (\Exception $e) {
        \Log::error('Failed to load sample data: ' . $e->getMessage());
    }
}
```

## Manual Commands

### Load Sample Data

```bash
# Load sample data (only if database is empty)
php artisan dps:load-sample-data

# Force reload sample data (clears existing data)
php artisan dps:load-sample-data --force
```

### Startup Script

Use the provided startup script for complete automation:

```bash
./start.sh
```

The startup script will:
1. Check for dependencies
2. Install missing packages
3. Generate application key
4. Run database migrations
5. Load sample data (if needed)
6. Build frontend assets
7. Start the development server

## Sample Data Included

### Users (5 total)
- **2 Admin Users:**
  - `admin@dps.gov.ph` / `admin123`
  - `hr@dps.gov.ph` / `admin123`
- **3 Employee Users:**
  - `john.smith@dps.gov.ph` / `employee123`
  - `maria.garcia@dps.gov.ph` / `employee123`
  - `robert.johnson@dps.gov.ph` / `employee123`

### Leave Rules (3)
- Emergency Leave: 5 credits
- Sick Leave: 5 credits
- Vacation Leave: 10 credits

### Leave Requests (9)
- Various statuses (pending, approved, rejected)
- Different leave types
- Realistic scenarios and details

### Payroll Records (6)
- 2 records per employee (1st and 2nd half of December 2024)
- Different salary levels and allowances
- Mix of approved and pending statuses

### Attendance Records (45)
- 15 days of attendance for each employee
- Regular 8-hour work days with some overtime

### Applicants & Applications (3 each)
- Sample applicants with different statuses
- Mix of pending, scheduled, and interviewed applications

## Configuration

### Environment Variables

The automated loading only runs in the local environment. To enable it in other environments, modify the service provider:

```php
// In SampleDataServiceProvider.php
if (app()->environment('local', 'staging') && $this->shouldLoadSampleData()) {
    $this->loadSampleData();
}
```

### Disabling Auto-Loading

To disable automatic sample data loading, comment out the service provider registration in `bootstrap/app.php`:

```php
->withProviders([
    // \App\Providers\SampleDataServiceProvider::class,
])
```

## Logging

The system logs when sample data is automatically loaded:

```
[2025-08-08 14:30:00] local.INFO: Sample data automatically loaded on application startup
```

Check the logs at `storage/logs/laravel.log` for more details.

## Troubleshooting

### Common Issues

1. **Sample data not loading:**
   - Check if database is properly configured
   - Verify migrations have been run
   - Check application logs for errors

2. **Foreign key constraint errors:**
   - The system handles this automatically by temporarily disabling foreign key checks
   - If issues persist, run `php artisan migrate:fresh` first

3. **Permission issues:**
   - Ensure the web server has write permissions to the storage directory
   - Run `chmod -R 755 storage bootstrap/cache`

### Manual Recovery

If automatic loading fails, you can manually load sample data:

```bash
# Clear database and reload
php artisan migrate:fresh
php artisan dps:load-sample-data --force
```

## Benefits

1. **Immediate Usability:** New installations have working data immediately
2. **Consistent Experience:** All users see the same sample data
3. **Testing Ready:** Developers can test with realistic scenarios
4. **Demo Friendly:** Perfect for demonstrations and presentations
5. **Training Tool:** New users can learn with sample data

## Security Notes

- Sample data is only loaded in local environment by default
- Passwords are hashed using Laravel's built-in hashing
- No sensitive real-world data is included
- All sample data is clearly marked as test data

---

**Note:** This feature is designed to make the DPS system immediately usable for new installations while maintaining security and data integrity.
