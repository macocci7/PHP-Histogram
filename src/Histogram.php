<?php

namespace Macocci7\PhpHistogram;

use Macocci7\PhpHistogram\Plotter;

class Histogram extends Plotter
{
    private const CANVAS_WIDTH_LIMIT_LOWER = 50;
    private const CANVAS_HEIGHT_LIMIT_LOWER = 50;

    /**
     * constructor
     * @param   int $width  default: 400(px as canvas width)
     * @param   int $height default: 300(px as canvas height)
     * @return  self
     */
    public function __construct(int $width = 400, int $height = 300)
    {
        parent::__construct();
        $this->resize($width, $height);
        return $this;
    }

    /**
     * returns config:
     * - of the specified key
     * - all configs if param is not specified
     * @param   string  $key    default: null
     * @return  mixed
     */
    public function getConfig(string $key = null)
    {
        if (null === $key) {
            $config = [];
            foreach ($this->validConfig as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (in_array($key, $this->validConfig)) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * judges if the param is in '#rrggbb' format or not
     * @param   mixed  $color
     * @return  bool
     */
    public function isColorCode($color)
    {
        if (!is_string($color)) {
            return false;
        }
        return preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $color) ? true : false;
    }

    /**
     * returns current canvas size
     * @param
     * @return  array   [width(px), height(px)]
     */
    public function size()
    {
        if (null === $this->canvasWidth || null === $this->canvasHeight) {
            return;
        }
        return [
            'width' => $this->canvasWidth,
            'height' => $this->canvasHeight,
        ];
    }

    /**
     * resizes the canvas size
     * @param   int $width  specify in pix at least 50
     * @param   int $height specify in pix at least 50
     * @return  self
     */
    public function resize(int $width, int $height)
    {
        if (!is_int($width) && !is_int($height)) {
            return;
        }
        if (
            $width < self::CANVAS_WIDTH_LIMIT_LOWER
            || $height < self::CANVAS_HEIGHT_LIMIT_LOWER
        ) {
            return;
        }
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    /**
     * sets the frame ratio of the histogram area to the canvas size
     * @param   float   $xRatio (0.0 < $xRatio < 1.0)
     * @param   float   $yRatio (0.0 < $yRatio < 1.0)
     * @return  self
     */
    public function frame(float $xRatio, float $yRatio)
    {
        if ($xRatio <= 0.0 || $xRatio > 1.0) {
            throw new \Exception("Ratio must be: 0.0 < ratio <= 1.0.");
        }
        if ($yRatio <= 0.0 || $yRatio > 1.0) {
            throw new \Exception("Ratio must be: 0.0 < ratio <= 1.0.");
        }
        $this->frameXRatio = $xRatio;
        $this->frameYRatio = $yRatio;
        return $this;
    }

    /**
     * sets the background color of the canvas
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     */
    public function bgcolor(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify the color code in '#rrggbb' format.");
        }
        $this->canvasBackgroundColor = $color;
        return $this;
    }

    /**
     * sets attributes of axis.
     * @param   int     $width  in pix
     * @param   string  $color  in '#rrggbb' format, null as default
     * @return  self
     */
    public function axis(int $width, $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (!is_null($color) && !$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->axisWidth = $width;
        if (!is_null($color)) {
            $this->axisColor = $color;
        }
        return $this;
    }

    /**
     * sets attributes of the grid
     * @param   int     $width  in pix
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     */
    public function grid(int $width, $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (null !== $color && !$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->gridWidth = $width;
        if (!is_null($color)) {
            $this->gridColor = $color;
        }
        return $this;
    }

    /**
     * sets the background color of histogram-bars
     * @param   string  $color in '#rrggbb' format
     * @return  self
     */
    public function color($color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->barBackgroundColor = $color;
        return $this;
    }

    /**
     * sets attributes of the border of histogram-bar
     * @param   int     $width  in pix
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     */
    public function border(int $width, $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (!is_null($color) && !$this->isColorCode($color)) {
            throw new \Exception("specify the color code in '#rrggbb' format.");
        }
        $this->barBorderWidth = $width;
        if (!is_null($color)) {
            $this->barBorderColor = $color;
        }
        return $this;
    }

    /**
     * sets attributes of the frequency polygon
     * @param   int     $width  in pix
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     */
    public function fp(int $width, $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (!is_null($color) && !$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->frequencyPolygonWidth = $width;
        if (!is_null($color)) {
            $this->frequencyPolygonColor = $color;
        }
        return $this;
    }

    /**
     * sets attributes of cumulative relative frequency polygon
     * @param   int     $width  in pix
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     */
    public function crfp(int $width, $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (!is_null($color) && !$this->isColorCode($color)) {
            return;
        }
        $this->cumulativeRelativeFrequencyPolygonWidth = $width;
        if (!is_null($color)) {
            $this->cumulativeRelativeFrequencyPolygonColor = $color;
        }
        return $this;
    }

    /**
     * sets the font path
     * @param   string  $path   is the real path to the true type font path
     * @return  self
     */
    public function fontPath(string $path)
    {
        if (!file_exists($path)) {
            throw new \Exception("file does not exist.");
        }
        $pathinfo = pathinfo($path);
        if (0 !== strcmp("ttf", strtolower($pathinfo['extension']))) {
            throw new \Exception("specify .ttf file path.");
        }
        $this->fontPath = $path;
        return $this;
    }

    /**
     * sets font size
     * @param   int $size
     * @return  self
     */
    public function fontSize(int $size)
    {
        if ($size < 6) {
            throw new \Exception("size must be more than 5.");
        }
        $this->fontSize = $size;
        return $this;
    }

    /**
     * sets font color
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     */
    public function fontColor($color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->fontColor = $color;
        return $this;
    }

    /**
     * sets label of X
     * @param   string  $label
     * @return  self
     */
    public function labelX(string $label)
    {
        $this->labelX = $label;
        return $this;
    }

    /**
     * sets label of Y
     * @param   string  $label
     * @return  self
     */
    public function labelY(string $label)
    {
        $this->labelY = $label;
        return $this;
    }

    /**
     * sets caption
     * @param   string  $caption
     * @return  self
     */
    public function caption(string $caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * sets bar-visibility on
     * @param
     * @return  self
     */
    public function barOn()
    {
        $this->showBar = true;
        return $this;
    }

    /**
     * sets var-visibility off
     * @param
     * @return  self
     */
    public function barOff()
    {
        $this->showBar = false;
        return $this;
    }

    /**
     * sets frequency-polygon-visibility on
     * @param
     * @return  self
     */
    public function fpOn()
    {
        $this->showFrequencyPolygon = true;
        return $this;
    }

    /**
     * sets frequency-polygon-visibility off
     * @param
     * @return  self
     */
    public function fpOff()
    {
        $this->showFrequencyPolygon = false;
        return $this;
    }

    /**
     * sets cumulative-relative-frequency-polygon-visibility on
     * @param
     * @return  self
     */
    public function crfpOn()
    {
        $this->showCumulativeRelativeFrequencyPolygon = true;
        return $this;
    }

    /**
     * sets cumulative-relative-frequency-polygon-visibility off
     * @param
     * @return  self
     */
    public function crfpOff()
    {
        $this->showCumulativeRelativeFrequencyPolygon = false;
        return $this;
    }

    /**
     * sets frequency-visibility on
     * @param
     * @return  self
     */
    public function frequencyOn()
    {
        $this->showFrequency = true;
        return $this;
    }

    /**
     * sets frequency-visibility off
     * @param
     * @return  self
     */
    public function frequencyOff()
    {
        $this->showFrequency = false;
        return $this;
    }
}
