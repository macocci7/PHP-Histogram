<?php

namespace Macocci7\PhpHistogram;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpFrequencyTable\FrequencyTable;

class Histogram
{
    public $ft;
    private $image;
    private $canvasWidth;
    private $canvasHeight;
    private $canvasBackgroundColor = '#ffffff';
    private $frameXRatio = 0.8;
    private $frameYRatio = 0.7;
    private $axisColor = '#666666';
    private $axisWidth = 2;
    private $gridColor = '#333333';
    private $gridWidth = 1;
    private $gridHeightPitch;
    private $barWidth;
    private $barHeightPitch;
    private $barBackgroundColor = '#0000ff';
    private $barBorderColor = '#9999ff';
    private $barBorderWidth = 1;
    private $frequencyPolygonColor = '#ff0000';
    private $frequencyPolygonWidth = 2;
    private $cumulativeRelativeFrequencyPolygonColor = '#33ff66';
    private $cumulativeRelativeFrequencyPolygonWidth = 2;
    private $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //private $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    private $fontSize = 16;
    private $fontColor = '#333333';
    private $barMaxValue;
    private $barMinValue;
    private $baseX;
    private $baseY;
    private $parsed = [];
    private $showBar = true;
    private $showFrequencyPolygon = false;
    private $showCumulativeRelativeFrequencyPolygon = false;
    private $showFrequency = false;
    private $labelX;
    private $labelY;
    private $caption;
    private $validConfig = [
        'canvasWidth',
        'canvasHeight',
        'canvasBackgroundColor',
        'frameXRatio',
        'frameYRatio',
        'axisColor',
        'axisWidth',
        'gridColor',
        'gridWidth',
        'gridHeightPitch',
        'barBackgroundColor',
        'barBorderColor',
        'barBorderWidth',
        'frequencyPolygonColor',
        'frequencyPolygonWidth',
        'cumulativeRelativeFrequencyPolygonColor',
        'cumulativeRelativeFrequencyPolygonWidth',
        'fontPath',
        'fontSize',
        'fontColor',
    ];

    /**
     * constructor
     * @param   int $width  default: 400(px as canvas width)
     * @param   int $height default: 300(px as canvas height)
     * @return  self
     */
    public function __construct(int $width = 400, int $height = 300)
    {
        Image::configure(['driver' => 'imagick']);
        $this->ft = new FrequencyTable();
        $this->resize($width, $height);
        return $this;
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
        if ($width < 50 || $height < 50) {
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
     * returns position of horizontal axis
     * @param
     * @return  array
     */
    public function getHorizontalAxisPosition()
    {
        return [
            (int) $this->baseX,
            (int) $this->baseY,
            (int) $this->canvasWidth * (1 + $this->frameXRatio) / 2,
            (int) $this->baseY,
        ];
    }

    /**
     * returns position of vertical axis
     * @param
     * @return  array
     */
    public function getVerticalAxisPosition()
    {
        return [
            (int) $this->baseX,
            (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2,
            (int) $this->baseX,
            (int) $this->baseY,
        ];
    }

    /**
     * plots axis
     * @param
     * @return  void
     */
    public function plotAxis()
    {
        list($x1,$y1,$x2,$y2) = $this->getHorizontalAxisPosition();
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->axisColor);
                $draw->width($this->axisWidth);
            }
        );
        list($x1,$y1,$x2,$y2) = $this->getVerticalAxisPosition();
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->axisColor);
                $draw->width($this->axisWidth);
            }
        );
    }

    /**
     * plots grids
     * @param
     * @return  void
     */
    public function plotGrids()
    {
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x1 = $this->baseX;
            $y1 = $this->baseY - $i * $this->barHeightPitch;
            $x2 = $this->canvasWidth * (1 + $this->frameXRatio) / 2;
            $y2 = $y1;
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->gridColor);
                    $draw->width($this->gridWidth);
                }
            );
            $x1 = $this->canvasWidth * (1 + $this->frameXRatio) / 2;
            $y1 = $this->baseY - $this->barMaxValue * $this->barHeightPitch;
            $x2 = $x1;
            $y2 = $this->baseY;
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->gridColor);
                    $draw->width($this->gridWidth);
                }
            );
        }
    }

    /**
     * plots grid values
     * @param
     * @return  void
     */
    public function plotGridValues()
    {
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x = $this->baseX - $this->fontSize * 1.1;
            $y = $this->baseY - $i * $this->barHeightPitch + $this->fontSize * 0.4;
            $this->image->text(
                $i,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
    }

    /**
     * returns position of the bar
     * @param   int $frequency
     * @param   int $index
     * @return  array
     */
    public function getBarPosition($frequency, $index)
    {
        return [
            (int) ($this->baseX + $index * $this->barWidth),
            (int) ($this->baseY - $this->barHeightPitch * $frequency),
            (int) ($this->baseX + ($index + 1) * $this->barWidth),
            (int) $this->baseY,
        ];
    }

    /**
     * plots bars
     * @param
     * @return  void
     */
    public function plotBars()
    {
        if (!array_key_exists('Classes', $this->parsed)) {
            throw new \Exception("Classes not found.");
        }
        if (!array_key_exists('Frequencies', $this->parsed)) {
            throw new \Exception("Frequencies not found.");
        }
        $classes = $this->parsed['Classes'];
        $frequencies = $this->parsed['Frequencies'];
        if (empty($classes) || empty($frequencies)) {
            throw new \Exception("Empty classes or frequencies.");
        }
        foreach ($classes as $index => $class) {
            list($x1,$y1,$x2,$y2) = $this->getBarPosition($frequencies[$index], $index);
            $this->image->rectangle(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->background($this->barBackgroundColor);
                    $draw->border($this->barBorderWidth, $this->barBorderColor);
                }
            );
        }
    }

    /**
     * plots classes
     * @param
     * @return  void
     */
    public function plotClasses()
    {
        if (!array_key_exists('Classes', $this->parsed)) {
            throw new \Exception("Classes not found.");
        }
        $classes = $this->parsed['Classes'];
        $x = $this->baseX;
        $y = $this->baseY + $this->fontSize * 1.2;
        $this->image->text(
            $classes[0]['bottom'],
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        foreach ($classes as $index => $class) {
            $x = $this->baseX + ($index + 1) * $this->barWidth;
            $y = $this->baseY + $this->fontSize * 1.2;
            $this->image->text(
                $class['top'],
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
    }

    /**
     * plots frequency polygon
     * @param
     * @return  void
     */
    public function plotFrequencyPolygon()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) {
            throw new \Exception("Frequencies not found.");
        }
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) {
            throw new \Exception("Frequencies not found.");
        }
        if (count($frequencies) < 2) {
            throw new \Exception("Too few frequencies.");
        }
        for ($i = 0; $i < count($frequencies) - 1; $i++) {
            $x1 = $this->baseX + ($i + 0.5) * $this->barWidth;
            $y1 = $this->baseY - $frequencies[$i] * $this->barHeightPitch;
            $x2 = $this->baseX + ($i + 1.5) * $this->barWidth;
            $y2 = $this->baseY - $frequencies[$i + 1] * $this->barHeightPitch;
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->frequencyPolygonColor);
                    $draw->width($this->frequencyPolygonWidth);
                }
            );
        }
    }

    /**
     * plots cumulative relative frequency polygon
     * @param
     * @return  void
     */
    public function plotCumulativeRelativeFrequencyPolygon()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) {
            throw new \Exception("Frequencies not found.");
        }
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) {
            throw new \Exception("Frequencies not found.");
        }
        if (count($frequencies) < 2) {
            throw new \Exception("Too few frequencies.");
        }
        $x1 = $this->baseX;
        $y1 = $this->baseY;
        $yTop = $this->canvasHeight * (1 - $this->frameYRatio) / 2;
        $ySpan = $this->baseY - $yTop;
        foreach ($frequencies as $index => $frequency) {
            $crf = $this->ft->getCumulativeRelativeFrequency($frequencies, $index);
            $x2 = $this->baseX + ($index + 1) * $this->barWidth;
            $y2 = $this->baseY - $ySpan * $crf;
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->cumulativeRelativeFrequencyPolygonColor);
                    $draw->width($this->cumulativeRelativeFrequencyPolygonWidth);
                }
            );
            $x1 = $x2;
            $y1 = $y2;
        }
    }

    /**
     * plots frequencies
     * @param
     * @return  void
     */
    public function plotFrequencies()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) {
            throw new \Exception("Frequencies not found.");
        }
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) {
            throw new \Exception("Frequencies not found.");
        }
        if (empty($frequencies)) {
            throw new \Exception("Frequencies not found.");
        }
        foreach ($frequencies as $index => $frequency) {
            $x = $this->baseX + ($index + 0.5) * $this->barWidth;
            $y = $this->baseY - $frequency * $this->barHeightPitch - $this->fontSize * 0.6;
            $this->image->text(
                $frequency,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
    }

    /**
     * plots label of X
     * @param
     * @return  self
     */
    public function plotLabelX()
    {
        $x = (int) $this->canvasWidth / 2;
        $y = $this->baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3 ;
        $this->image->text(
            (string) $this->labelX,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        return $this;
    }

    /**
     * plots label of Y
     * @param
     * @return  self
     */
    public function plotLabelY()
    {
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        $image = Image::canvas($width, $height, $this->canvasBackgroundColor);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $image->text(
            (string) $this->labelY,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        $image->rotate(90);
        $this->image->insert($image, 'left');
        return $this;
    }

    /**
     * plots caption
     * @param
     * @return  void
     */
    public function plotCaption()
    {
        $x = $this->canvasWidth / 2;
        $y = $this->canvasHeight * (1 - $this->frameYRatio) / 3;
        $this->image->text(
            (string) $this->caption,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
    }

    /**
     * sets properties
     * @param
     * @return  void
     */
    private function setProperties()
    {
        $this->parsed = $this->ft->parse();
        $this->baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $this->baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        $this->barMaxValue = max($this->parsed['Frequencies']) + 1;
        $this->barMinValue = 0;
        $this->barWidth = $this->canvasWidth * $this->frameXRatio / count($this->parsed['Classes']);
        $this->barHeightPitch = $this->canvasHeight * $this->frameYRatio / $this->barMaxValue;
        $this->gridHeightPitch = 1;
        if ($this->gridHeightPitch < 0.2 * $this->barMaxValue) {
            $this->gridHeightPitch = (int) (0.2 * $this->barMaxValue);
        }
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
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

    /**
     * creates a histogram image
     * @param   string  $filePath
     * @return  self
     */
    public function create(string $filePath)
    {
        if (strlen($filePath) === 0) {
            throw new \Exception("specify a file path to save image.");
        }
        $this->setProperties();
        $this->plotGrids();
        $this->plotGridValues();
        if ($this->showBar) {
            $this->plotBars();
        }
        $this->plotAxis();
        if ($this->showFrequencyPolygon) {
            $this->plotFrequencyPolygon();
        }
        if ($this->showCumulativeRelativeFrequencyPolygon) {
            $this->plotCumulativeRelativeFrequencyPolygon();
        }
        $this->plotClasses();
        if ($this->showFrequency) {
            $this->plotFrequencies();
        }
        $this->plotLabelX();
        $this->plotLabelY();
        $this->plotCaption();
        $this->image->save($filePath);
        return $this;
    }
}
