<?php

namespace Macocci7\PhpHistogram\Traits;

trait StyleTrait
{
    protected string|null $canvasBackgroundColor;
    protected string|null $axisColor;
    protected int $axisWidth;
    protected string|null $gridColor;
    protected int $gridWidth;
    protected string|null $barBackgroundColor;
    protected string|null $barBorderColor;
    protected int $barBorderWidth;
    protected string|null $frequencyPolygonColor;
    protected int $frequencyPolygonWidth;
    protected string|null $cumulativeRelativeFrequencyPolygonColor;
    protected int $cumulativeRelativeFrequencyPolygonWidth;
    protected string $fontPath;
    protected int $fontSize;
    protected string|null $fontColor;

    /**
     * sets the background color of the canvas
     * @param   string  $color  in '#rrggbb' format
     * @return  self
     * @thrown  \Exception
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
     * @param   int         $width  in pix
     * @param   string|null $color  in '#rrggbb' format, null as default
     * @return  self
     * @thrown  \Exception
     */
    public function axis(int $width, string|null $color = null)
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
     * @param   int         $width  in pix
     * @param   string|null $color  in '#rrggbb' format
     * @return  self
     * @thrown  \Exception
     */
    public function grid(int $width, string|null $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (!is_null($color) && !$this->isColorCode($color)) {
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
     * @thrown  \Exception
     */
    public function color(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->barBackgroundColor = $color;
        return $this;
    }

    /**
     * sets attributes of the border of histogram-bar
     * @param   int         $width  in pix
     * @param   string|null $color  in '#rrggbb' format
     * @return  self
     * @thrown  \Exception
     */
    public function border(int $width, string|null $color = null)
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
     * @param   int         $width  in pix
     * @param   string|null $color  in '#rrggbb' format
     * @return  self
     * @thrown  \Exception
     */
    public function fp(int $width, string|null $color = null)
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
     * @param   int         $width  in pix
     * @param   string|null $color  in '#rrggbb' format
     * @return  self
     * @thrown  \Exception
     */
    public function crfp(int $width, string|null $color = null)
    {
        if ($width < 1) {
            throw new \Exception("width must be more than zero.");
        }
        if (!is_null($color) && !$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
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
     * @thrown  \Exception
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
     * @thrown  \Exception
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
     * @thrown  \Exception
     */
    public function fontColor(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->fontColor = $color;
        return $this;
    }
}
