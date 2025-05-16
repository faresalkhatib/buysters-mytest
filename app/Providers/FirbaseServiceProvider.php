<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Firestore;

class FirbaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'));

        $this->app->singleton('firebase.auth', function () use ($factory): Auth {
            return $factory->createAuth();
        });

        $this->app->singleton('firebase.database', function () use ($factory): Database {
            return $factory->createDatabase();
        });

        $this->app->singleton('firebase.firestore', function () use ($factory) {
            return $factory->createFirestore()->database();
        });

        $this->app->singleton('firebase.storage', function () use ($factory) {
            return $factory->createStorage();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
