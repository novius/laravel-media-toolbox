<?php

namespace Novius\MediaToolbox\Support;

use Illuminate\Contracts\Support\Htmlable;

class QueryBuilder implements Htmlable
{
    public $query;

    public function __construct($query = null)
    {
        $this->query = $query ?: new Query;
    }

    public function toHtml()
    {
        return (string) $this;
    }

    public function __toString()
    {
        return route('mediatoolbox.query', $this->query);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new self, $name], $arguments);
    }

    public function asset($src)
    {
        $this->query->o = $src;

        return $this;
    }
    
    public function quality($percent)
    {
        if ($percent) {
            $this->query->c = $percent;
        }

        return $this;
    }

    public function size($size)
    {
        if (!is_array($size)) {
            $size = func_get_args();
        }
        list($width, $height) = $size + [null, null];

        return $this->width($width)->height($height);
    }

    public function width($size)
    {
        if ($size) {
            $this->query->w = $size;
        }

        return $this;
    }

    public function height($size)
    {
        if ($size) {
            $this->query->h = $size;
        }

        return $this;
    }

    public function ratio($ratio)
    {
        if (func_num_args() > 1) {
            list($x, $y) = func_get_args();
            $ratio = $x / $y;
        }
        
        if ($this->query->w) {
            $this->query->h = $this->query->w / $ratio;
        }elseif ($this->query->h) {
            $this->query->w = $this->query->h * $ratio;
        }

        return $this;
    }
}
