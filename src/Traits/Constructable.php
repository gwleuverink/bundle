<?php

namespace Leuverink\Bundle\Traits;

use Throwable;

trait Constructable
{
    public static function new(): self
    {
        $interfaces = class_implements(static::class);

        if($interfaces) {
            try {
                return resolve(head($interfaces), func_get_args());
            } catch (Throwable $e) {}
        }

        return new static(...func_get_args());
    }
}
