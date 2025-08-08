<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use App\Models\Application;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        

        
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'pendingCount' => Application::where('status', 'PENDING')->count(),
                'acceptedCount' => Application::where('status', 'ACCEPTED')->count(),
                'rejectedCount' => Application::where('status', 'REJECTED')->count(),
            ],
            'routeName' => $request->route()->getName(),
        ];
    }
}
