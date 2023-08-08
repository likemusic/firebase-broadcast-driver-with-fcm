<?php

namespace ctf0\Firebase;

use ctf0\Firebase\Broadcasters\FSDB;
use ctf0\Firebase\Broadcasters\RTDB;
use ctf0\Firebase\Broadcasters\FCM;
use Illuminate\Support\ServiceProvider;
use Illuminate\Broadcasting\BroadcastManager;

class FireBaseBroadcastProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        app(BroadcastManager::class)->extend('firebase', function ($app) {
            $config = config('broadcasting.connections.firebase');

            switch($config['type']) {
                case 'database': return new RTDB($config);
                case 'messaging': return new FCM($config);
                case 'firestore': return new FSDB($config);
                default: return new FSDB($config);
            }
        });
    }

    public function register()
    {
    }
}
