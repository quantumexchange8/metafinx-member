<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

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
    public function version(Request $request): string|null
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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'auth.user.picture' => fn() => $request->user() ? $request->user()->getFirstMediaUrl('profile_photo') : null,
            'auth.user.notifications' => fn() => $request->user() ? $request->user()->notifications : null,
            'auth.user.readNotifications' => fn() => $request->user() ? $request->user()->readNotifications : null,
            'auth.user.unreadNotifications' => fn() => $request->user() ? $request->user()->unreadNotifications : null,
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'csrf_token' => csrf_token(),
            'locale' => session('locale') ? session('locale') : app()->getLocale(),
            'title' => session('title'),
            'success' => session('success'),
            'warning' => session('warning'),
            'toast' => session('toast'),
        ];
    }
}
