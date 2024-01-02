<?php

namespace Leuverink\Bundle\Traits;

trait Constructable
{
    public static function construct(): self
    {
        return resolve(static::class, func_get_args());
    }
}
