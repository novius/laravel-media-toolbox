<?php

namespace Novius\MediaToolbox\Support;

interface OptimizerInterface
{
    public function loadFromFile(string $filename): self;

    public function getOptimizedContent(): string;
}
