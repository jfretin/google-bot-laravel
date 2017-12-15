<?php

namespace Tudorica\GoogleBot;

use Illuminate\Support\ServiceProvider;
use Tudorica\GoogleBot\Services\BotService;

class GoogleBotServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * @return void
     */
    public function boot()
    {
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('googlebot', function ($app) {
            return new BotService();
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['googlebot'];
    }
}