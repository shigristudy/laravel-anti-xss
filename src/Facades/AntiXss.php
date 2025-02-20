<?php

namespace Kabeer\LaravelAntiXss\Facades;

use Illuminate\Support\Facades\Facade;

class AntiXss extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'anti-xss';
    }
}
