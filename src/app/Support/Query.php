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
        return http_build_query($this);
    }
}
