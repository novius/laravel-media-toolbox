<?php

namespace Novius\MediaToolbox\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    public $query;
    public $optimizer;
    public $path;
    public $mimetype;

    public function __construct(Query $query)
    {
        $this->query = $query;
        $this->optimizer = new ImageOptimizer;
        $trimmed = ltrim($this->query->o, '/');
        $this->optimizer->loadFromFile((file_exists($trimmed) ? $trimmed : $this->query->o));
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

    public function persists(string $filename): bool
    {
        $this->process();
        $content = $this->optimizer->getOptimizedContent();

        return Storage::disk(config('mediatoolbox.disk', 'public'))
            ->put(config('mediatoolbox.medias_dirname').DIRECTORY_SEPARATOR.$filename, $content);
    }

    public function stream()
    {
        return response(
            $this->getRawContent(),
            200,
            ['Content-Type' => $this->mimetype]
        );
    }

    protected function getRawContent(): string
    {
        $this->process();
        $content = $this->optimizer->getOptimizedContent();
        $this->mimetype = $this->optimizer->mimetype;

        return $content;
    }
}
