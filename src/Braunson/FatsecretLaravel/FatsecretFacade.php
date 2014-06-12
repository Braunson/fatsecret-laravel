<?php namespace Braunson\FatsecretLaravel;

use Illuminate\Support\Facades\Facade;

class FatsecretFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'fatsecret';
    }
}
