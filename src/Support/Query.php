<?php

namespace Novius\MediaToolbox\Support;

class Query
{
    // original file
    public $o;

    // destination width
    public $w;

    // destination height
    public $h;

    // fit: cover, stretch
    public $f;

    // jpg quality in percentage
    public $c;

    // SEO friendly name for filename
    public $n;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function __toString()
    {
        $args = (array) $this;
        ksort($args);

        return http_build_query($args, null, '&');
    }

    public function hash() : ?string
    {
        if (empty($this->o)) {
            return null;
        }

        return md5((string) $this);
    }

    public function filename() : ?string
    {
        if (!empty($this->n)) {
            return $this->n;
        }

        return $this->originalFilename();
    }

    protected function originalFilename() : ?string
    {
        if (empty($this->o)) {
            return null;
        }

        return pathinfo($this->o, PATHINFO_FILENAME);
    }

    public function extension() : ?string
    {
        if (empty($this->o)) {
            return null;
        }

        return pathinfo($this->o, PATHINFO_EXTENSION);
    }
}
