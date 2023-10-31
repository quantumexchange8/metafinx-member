<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);
        $status = $response->status();

        if (!app()->environment(['local', 'testing'])) {

            return match ($status) {
                404 => Inertia::render('Errors/404')->toResponse($request)->setStatusCode($status),
                500, 503 => Inertia::render('Errors/500')->toResponse($request)->setStatusCode($status),
                403 => Inertia::render('Errors/403')->toResponse($request)->setStatusCode($status),
                419 => redirect()->back()->with('title', 'Page Expired')->with('warning', 'Please try again later.'),
                default => $response
            };

        }

        return $response;

    }
}
