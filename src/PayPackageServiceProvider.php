<?php

namespace iProtek\Dbm;

use Illuminate\Support\ServiceProvider;

class DbmPackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register package services
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Bootstrap package services
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'iprotek_pay');

        
        $target = __DIR__.'/../public';
        $target = realpath($target);

        if($target){
            $link = public_path('iprotek');
            // Create symbolic link if it doesn't exist
            if (!file_exists($link)) {
                symlink($target, $link);
            }
        }

        $this->mergeConfigFrom(
            __DIR__ . '/../config/iprotek.php', 'iprotek_pay'
        );
    }
}