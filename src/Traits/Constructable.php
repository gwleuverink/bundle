<?php

namespace Leuverink\Bundle\Traits;

trait Constructable
{
    public static function new(): self
    {
        $interfaces = class_implements(static::class);

        if($interfaces) {
            return resolve(head($interfaces), func_get_args());
        }

        return resolve(static::class, func_get_args());
    }
}
