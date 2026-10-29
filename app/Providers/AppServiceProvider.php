<?php

namespace App\Providers;

use App\Http\Middleware\Tenancy\DetectTenant;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
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
        Livewire::addPersistentMiddleware([
            DetectTenant::class,
        ]);
        Mail::alwaysFrom(config('mail.from.address'), emailFromName());
    }
}
