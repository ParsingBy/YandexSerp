<?php

namespace ParsingBy\YandexSerp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ParsingBy\YandexSerp\Skeleton\SkeletonClass
 */
class YandexSerpPositionsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'yandexserppositions';
    }
}
