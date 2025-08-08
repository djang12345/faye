<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\LeaveRule;

class SampleDataServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only run in local environment and when database is empty
        if (app()->environment('local') && $this->shouldLoadSampleData()) {
            $this->loadSampleData();
        }
    }

    /**
     * Check if sample data should be loaded
     */
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

    /**
     * Load sample data
     */
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
}
