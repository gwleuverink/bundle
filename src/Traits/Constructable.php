<?php

// @codeCoverageIgnoreStart

namespace Leuverink\Bundle\Traits;

use Throwable;

trait Constructable
{
    public static function new(): mixed
    {
        $interfaces = class_implements(static::class);

        if ($interfaces) {
            try {
                return resolve(head($interfaces), func_get_args());
            } catch (Throwable $e) {
            }
        }

        return new self(...func_get_args()); /** @phpstan-ignore-line */
    }
}

// @codeCoverageIgnoreEnd
