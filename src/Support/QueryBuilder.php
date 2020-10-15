<?php

namespace Novius\MediaToolbox\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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
        $pictureInfos = Cache::remember($this->cacheKey(), config('mediatoolbox.expire'), function () {
            $filename = uniqid($this->query->filename().'-');
            if (!empty($this->query->extension())) {
                $filename .= '.'.$this->query->extension();
            }

            Cache::put('mediatoolbox.media.'.$filename, $this->cacheKey(), config('mediatoolbox.expire'));

            return [
                'filename' => $filename,
                'query' => (array) $this->query,
            ];
        });

        return route('mediatoolbox.query', [
            'query' => $pictureInfos['filename'],
        ]);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new self, $name], $arguments);
    }

    public function asset(string $src)
    {
        $this->query->o = $src;

        return $this;
    }

    public function quality(int $percent)
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

        return $this->width((int) $width)->height((int) $height);
    }

    public function width(int $size)
    {
        if ($size) {
            $this->query->w = $size;
        }

        return $this;
    }

    public function height(int $size)
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
        } elseif ($this->query->h) {
            $this->query->w = $this->query->h * $ratio;
        }

        return $this;
    }

    public function name(string $name)
    {
        $this->query->n = Str::slug($name);

        return $this;
    }

    public function cacheKey():string
    {
        $queryHash = $this->query->hash();
        if (!is_string($queryHash)) {
            $queryHash = 'invalid';
        }

        return 'mediatoolbox.query.'.$queryHash;
    }
}
