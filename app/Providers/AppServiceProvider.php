<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use Illuminate\Mail\MailManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Mail\Transports\BrevoTransport;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind mail manager and register custom brevo mailer
        $this->app->bind('mail.manager', function ($app) {
            return new \Illuminate\Mail\MailManager($app);
        });

        $this->app->afterResolving(MailManager::class, function ($mailManager) {
            $mailManager->extend('brevo', function () {
                return new BrevoTransport(
                    app(HttpClientInterface::class),
                    config('services.brevo.api_key')
                );
            });
        });
    }

    public function boot()
    {
        View::composer('*', function ($view) {
            $categories = Category::all();
            $view->with('categories', $categories);
        });

        // Removed: Livewire Filament TestWidget
    }
}
