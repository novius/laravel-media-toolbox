<?php

namespace Novius\MediaToolbox\Support;

class MediaToolbox
{
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new QueryBuilder, $name], $arguments);
    }
}
