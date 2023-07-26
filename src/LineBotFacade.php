<?php

namespace Stephenchen\LineBot;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stephenchen\LineBot\Skeleton\SkeletonClass
 */
class LineBotFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'line-bot';
    }
}
