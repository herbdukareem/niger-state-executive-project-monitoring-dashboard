<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(fn () => view('app'));
        Fortify::registerView(fn () => view('app'));
        Fortify::requestPasswordResetLinkView(fn () => view('app'));
        Fortify::resetPasswordView(fn () => view('app'));
        Fortify::verifyEmailView(fn () => view('app'));
        Fortify::twoFactorChallengeView(fn () => view('app'));
        Fortify::confirmPasswordView(fn () => view('app'));

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
