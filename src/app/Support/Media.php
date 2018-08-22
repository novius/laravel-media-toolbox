<?php

namespace Novius\MediaToolbox\Support;

use Illuminate\Support\Facades\Cache;

class Media
{
    public $query;
    public $optimizer;
    public $mimetype;

    public function __construct(Query $query)
    {
        $this->query = $query;
        $this->optimizer = new ImageOptimizer;
        $this->optimizer->loadFromFile(
            file_exists($trimmed = ltrim($this->query->o, '/')) ?
            $trimmed : $this->query->o
        );
    }

    public function process()
    {
        // Process resizing
        if ($this->query->w && $this->query->h) {
            $this->optimizer->resize(
                intval($this->query->w),
                intval($this->query->h),
                $this->query->f ?: 'cover'
            );
        } elseif ($this->query->w) {
            $this->optimizer->resizeToWidth(
                intval($this->query->w)
            );
        } elseif ($this->query->h) {
            $this->optimizer->resizeToHeight(
                intval($this->query->h)
            );
        }

        // Process compression change
        if ($this->query->c) {
            $this->optimizer->compress($this->query->c);
        }
    }

    public function stream()
    {
        return response(
            $this->getRawContent(),
            200,
            ['Content-Type' => $this->mimetype]
        );
    }

    public function getRawContent()
    {
        $key = 'mediatoolbox-'.$this->buildHash();

        $cache = Cache::store(config('imagetoolbox.cache', 'file'));

        if ($cache->has($key)) {
            // Retreive picture from cache
            $content = $cache->get($key);
            $this->mimetype = getimagesizefromstring($content)['mime'];

            return $content;
        }

        $this->process();
        $content = $this->optimizer->getOptimizedContent();
        $this->mimetype = $this->optimizer->mimetype;

        // Store generated picture in cache
        $cache->put(
            $key,
            $content,
            now()->addMinutes(config('imagetoolbox.expire', 10))
        );

        return $content;
    }

    public function buildHash()
    {
        $time = file_exists($trimmed = ltrim($this->query->o, '/')) ?
            filemtime(public_path($trimmed)) : null;

        return md5($time.$this->query);
    }
}
