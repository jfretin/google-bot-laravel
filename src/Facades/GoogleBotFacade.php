<?php

namespace Tudorica\GoogleBot\Facades;

use \Illuminate\Support\Facades\Facade;

class GoogleBotFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'googlebot';
    }
}