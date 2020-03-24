<?php

namespace Novius\MediaToolbox\Support;

class ImageOptimizer implements OptimizerInterface
{
    public $image;
    public $mimetype;
    public $compression;

    protected static $methods = [
        'image/jpeg' => [
            'create' => 'imagecreatefromjpeg',
            'output' => 'imagejpeg',
        ],
        'image/gif' => [
            'create' => 'imagecreatefromgif',
            'output' => 'imagegif',
        ],
        'image/png' => [
            'create' => 'imagecreatefrompng',
            'output' => 'imagepng',
        ],
    ];

    public function loadFromFile(string $filename): OptimizerInterface
    {
        try {
            $this->mimetype = getimagesize($filename)['mime'];
            $this->image = self::$methods[$this->mimetype]['create']($filename);
        }
        catch (\Exception $e) {
            throw new \Exception('Source image format not supported', 1);
        }

        return $this;
    }

    public function getOptimizedContent(): string
    {
        $contents = '';

        try {
            $stream = fopen('php://memory', 'w+');

            $this->compression ?
                self::$methods[$this->mimetype]['output']
                    ($this->image, $stream, $this->compression) :
                self::$methods[$this->mimetype]['output']
                    ($this->image, $stream);
            rewind($stream);
            $contents = stream_get_contents($stream);
            fclose($stream);
        }
        catch (\Exception $e) {
            throw new \Exception('Error generating image', 2);
        }

        return $contents;
    }

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function resizeToHeight($height)
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width, $height, 'stretch');
    }

    public function resizeToWidth($width)
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width, $height, 'stretch');
    }

    public function scale($scale)
    {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width, $height, 'stretch');
    }

    public function resize($width, $height, $fit)
    {
        $newImage = imagecreatetruecolor($width, $height);

        if ($fit == 'cover') {
            $newWidth = $this->getHeight() * $width / $height;
            $newHeight = $this->getWidth() * $height / $width;
            if ($newWidth > $this->getWidth()) {
                $sourceX = 0;
                $sourceY = ($this->getHeight() - $newHeight) / 2;
                $sourceWidth = $this->getWidth();
                $sourceHeight = $newHeight;
            } else {
                $sourceX = ($this->getWidth() - $newWidth) / 2;
                $sourceY = 0;
                $sourceWidth = $newWidth;
                $sourceHeight = $this->getHeight();
            }
        }
        elseif ($fit == 'stretch') {
            $sourceX = $sourceY = 0;
            $sourceWidth = $this->getWidth();
            $sourceHeight = $this->getHeight();
        }
        else {
            throw new \Exception('Fitting mode not supported', 3);
        }

        imagecopyresampled(
            $newImage, $this->image,
            0, 0, $sourceX, $sourceY,
            $width, $height, $sourceWidth, $sourceHeight
        );

        $this->image = $newImage;
    }

    public function compress($percentage)
    {
        $this->compression = intval($percentage);
        $this->mimetype = 'image/jpeg';
    }
}
