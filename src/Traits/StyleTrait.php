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
    protected string $fontPath = __DIR__ . '/../Fonts/ipaexg.ttf';
    protected int $fontSize;
    protected string|null $fontColor;

    /**
     * sets the background color of the canvas
     * @thrown  \Exception
     */
    public function bgcolor(string $color): self
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify the color code in '#rrggbb' format.");
        }
        $this->canvasBackgroundColor = $color;
        return $this;
    }

    /**
     * sets attributes of axis.
     * @thrown  \Exception
     */
    public function axis(int $width, string|null $color = null): self
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
     * @thrown  \Exception
     */
    public function grid(int $width, string|null $color = null): self
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
     * @thrown  \Exception
     */
    public function color(string $color): self
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->barBackgroundColor = $color;
        return $this;
    }

    /**
     * sets attributes of the border of histogram-bar
     * @thrown  \Exception
     */
    public function border(int $width, string|null $color = null): self
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
     * @thrown  \Exception
     */
    public function fp(int $width, string|null $color = null): self
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
     * @thrown  \Exception
     */
    public function crfp(int $width, string|null $color = null): self
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
     * @thrown  \Exception
     */
    public function fontPath(string $path): self
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
     * @thrown  \Exception
     */
    public function fontSize(int $size): self
    {
        if ($size < 6) {
            throw new \Exception("size must be more than 5.");
        }
        $this->fontSize = $size;
        return $this;
    }

    /**
     * sets font color
     * @thrown  \Exception
     */
    public function fontColor(string $color): self
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify color code in '#rrggbb' format.");
        }
        $this->fontColor = $color;
        return $this;
    }
}
