<?php

namespace Macocci7\PhpHistogram;

use Macocci7\PhpHistogram\Helpers\Config;
use Macocci7\PhpHistogram\Traits\JudgeTrait;
use Macocci7\PhpHistogram\Plotter;
use Nette\Neon\Neon;

/**
 * Class for Histogram operation
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class Histogram extends Plotter
{
    use JudgeTrait;

    private int $CANVAS_WIDTH_LIMIT_LOWER;
    private int $CANVAS_HEIGHT_LIMIT_LOWER;

    /**
     * constructor
     * @param   int $width  default: 400(px as canvas width)
     * @param   int $height default: 300(px as canvas height)
     */
    public function __construct(int $width = 400, int $height = 300)
    {
        parent::__construct();
        $this->loadConf();
        $this->resize($width, $height);
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'CANVAS_WIDTH_LIMIT_LOWER',
            'CANVAS_HEIGHT_LIMIT_LOWER',
        ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop);
        }
    }

    /**
     * set config from specified resource
     * @param   string|mixed[]  $configResource
     * @return  self
     */
    public function config(string|array $configResource)
    {
        if (is_string($configResource)) {
            $conf = $this->configFromFile($configResource);
        } else {
            $conf = $this->configFromArray($configResource);
        }
        foreach ($conf as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    /**
     * returns valid config items from specified file
     * @param   string  $path
     * @return  mixed[]
     * @thrown  \Exception
     */
    private function configFromFile(string $path)
    {
        if (strlen($path) === 0) {
            throw new \Exception("Specify valid filename.");
        }
        if (!is_readable($path)) {
            throw new \Exception("Cannot read file $path.");
        }
        $content = Neon::decodeFile($path);
        return $this->configFromArray($content);
    }

    /**
     * returns valid config items from specified array
     * @param   mixed[] $content
     * @return  mixed[]
     * @thrown  \Exception
     */
    private function configFromArray(array $content)
    {
        $conf = [];
        foreach ($this->validConfig as $key => $def) {
            if (isset($content[$key])) {
                if (Config::isValid($content[$key], $def['type'])) {
                    $conf[$key] = $content[$key];
                } else {
                    $message = $key . " must be type of " . $def['type'] . ".";
                    throw new \Exception($message);
                }
            }
        }
        return $conf;
    }

    /**
     * returns config:
     * - of the specified key
     * - all configs if param is not specified
     * @param   string|null $key    default: null
     * @return  mixed
     */
    public function getConfig(string|null $key = null)
    {
        if (is_null($key)) {
            $config = [];
            foreach (array_keys($this->validConfig) as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (isset($this->validConfig[$key])) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * returns current canvas size
     * @return  array<string, int>  [width(px), height(px)]
     */
    public function size()
    {
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
     * @thrown  \Exception
     */
    public function resize(int $width, int $height)
    {
        if ($width < $this->CANVAS_WIDTH_LIMIT_LOWER) {
            throw new \Exception(
                "width is below the lower limit "
                . $this->CANVAS_WIDTH_LIMIT_LOWER
            );
        }
        if ($height < $this->CANVAS_HEIGHT_LIMIT_LOWER) {
            throw new \Exception(
                "height is below the lower limit "
                . $this->CANVAS_HEIGHT_LIMIT_LOWER
            );
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
     * @thrown  \Exception
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
     * @return  self
     */
    public function barOn()
    {
        $this->showBar = true;
        return $this;
    }

    /**
     * sets var-visibility off
     * @return  self
     */
    public function barOff()
    {
        $this->showBar = false;
        return $this;
    }

    /**
     * sets frequency-polygon-visibility on
     * @return  self
     */
    public function fpOn()
    {
        $this->showFrequencyPolygon = true;
        return $this;
    }

    /**
     * sets frequency-polygon-visibility off
     * @return  self
     */
    public function fpOff()
    {
        $this->showFrequencyPolygon = false;
        return $this;
    }

    /**
     * sets cumulative-relative-frequency-polygon-visibility on
     * @return  self
     */
    public function crfpOn()
    {
        $this->showCumulativeRelativeFrequencyPolygon = true;
        return $this;
    }

    /**
     * sets cumulative-relative-frequency-polygon-visibility off
     * @return  self
     */
    public function crfpOff()
    {
        $this->showCumulativeRelativeFrequencyPolygon = false;
        return $this;
    }

    /**
     * sets frequency-visibility on
     * @return  self
     */
    public function frequencyOn()
    {
        $this->showFrequency = true;
        return $this;
    }

    /**
     * sets frequency-visibility off
     * @return  self
     */
    public function frequencyOff()
    {
        $this->showFrequency = false;
        return $this;
    }
}
