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

    public function __construct($width = 400, $height = 300)
    {
        Image::configure(['driver' => 'imagick']);
        $this->ft = new FrequencyTable();
        $this->resize($width, $height);
        return $this;
    }

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

    public function resize($width, $height)
    {
        if (!is_int($width) && !is_int($height)) {
            return;
        }
        if ($width < 100 || $height < 100) {
            return;
        }
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    public function frame($xRatio, $yRatio)
    {
        if (!is_float($xRatio) || !is_float($yRatio)) {
            return;
        }
        if ($xRatio <= 0.0 || $xRatio > 1.0) {
            return;
        }
        if ($yRatio <= 0.0 || $yRatio > 1.0) {
            return;
        }
        $this->frameXRatio = $xRatio;
        $this->frameYRatio = $yRatio;
        return $this;
    }

    public function bgcolor($color)
    {
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->canvasBackgroundColor = $color;
        return $this;
    }

    public function axis($width, $color = null)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (null !== $color && !$this->isColorCode($color)) {
            return;
        }
        $this->axisWidth = $width;
        if (null !== $color) {
            $this->axisColor = $color;
        }
        return $this;
    }

    public function grid($width, $color = null)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (null !== $color && !$this->isColorCode($color)) {
            return;
        }
        $this->gridWidth = $width;
        if (null !== $color) {
            $this->gridColor = $color;
        }
        return $this;
    }

    public function color($color)
    {
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->barBackgroundColor = $color;
        return $this;
    }

    public function border($width, $color)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->barBorderWidth = $width;
        $this->barBorderColor = $color;
        return $this;
    }

    public function fp($width, $color)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->frequencyPolygonWidth = $width;
        $this->frequencyPolygonColor = $color;
        return $this;
    }

    public function crfp($width, $color)
    {
        if (!is_int($width)) {
            return;
        }
        if ($width < 1) {
            return;
        }
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->cumulativeRelativeFrequencyPolygonWidth = $width;
        $this->cumulativeRelativeFrequencyPolygonColor = $color;
        return $this;
    }

    public function fontPath($path)
    {
        if (!is_string($path)) {
            return;
        }
        if (strlen($path) < 5) {
            return;
        }
        if (!file_exists($path)) {
            return;
        }
        $pathinfo = pathinfo($path);
        if (0 !== strcmp("ttf", strtolower($pathinfo['extension']))) {
            return;
        }
        $this->fontPath = $path;
        return $this;
    }

    public function fontSize($size)
    {
        if (!is_int($size)) {
            return;
        }
        if ($size < 6) {
            return;
        }
        $this->fontSize = $size;
        return $this;
    }

    public function fontColor($color)
    {
        if (!$this->isColorCode($color)) {
            return;
        }
        $this->fontColor = $color;
        return $this;
    }

    public function isColorCode($color)
    {
        if (!is_string($color)) {
            return false;
        }
        return preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $color) ? true : false;
    }

    public function getConfig($key = null)
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

    public function getHorizontalAxisPosition()
    {
        return [
            (int) $this->baseX,
            (int) $this->baseY,
            (int) $this->canvasWidth * (1 + $this->frameXRatio) / 2,
            (int) $this->baseY,
        ];
    }

    public function getVerticalAxisPosition()
    {
        return [
            (int) $this->baseX,
            (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2,
            (int) $this->baseX,
            (int) $this->baseY,
        ];
    }

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

    public function getBarPosition($frequency, $index)
    {
        return [
            (int) ($this->baseX + $index * $this->barWidth),
            (int) ($this->baseY - $this->barHeightPitch * $frequency),
            (int) ($this->baseX + ($index + 1) * $this->barWidth),
            (int) $this->baseY,
        ];
    }

    public function plotBars()
    {
        if (!array_key_exists('Classes', $this->parsed)) {
            return;
        }
        if (!array_key_exists('Frequencies', $this->parsed)) {
            return;
        }
        $classes = $this->parsed['Classes'];
        $frequencies = $this->parsed['Frequencies'];
        if (empty($classes) || empty($frequencies)) {
            return;
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

    public function plotClasses()
    {
        if (!array_key_exists('Classes', $this->parsed)) {
            return;
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

    public function plotFrequencyPolygon()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) {
            return;
        }
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) {
            return;
        }
        if (count($frequencies) < 2) {
            return;
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

    public function plotCumulativeRelativeFrequencyPolygon()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) {
            return;
        }
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) {
            return;
        }
        if (count($frequencies) < 2) {
            return;
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

    public function plotFrequencies()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) {
            return;
        }
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) {
            return;
        }
        if (empty($frequencies)) {
            return;
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

    public function labelX($label)
    {
        if (!is_string($label)) {
            return;
        }
        $this->labelX = $label;
        return $this;
    }

    public function labelY($label)
    {
        if (!is_string($label)) {
            return;
        }
        $this->labelY = $label;
        return $this;
    }

    public function caption($caption)
    {
        if (!is_string($caption)) {
            return;
        }
        $this->caption = $caption;
        return $this;
    }

    public function barOn()
    {
        $this->showBar = true;
        return $this;
    }

    public function barOff()
    {
        $this->showBar = false;
        return $this;
    }

    public function fpOn()
    {
        $this->showFrequencyPolygon = true;
        return $this;
    }

    public function fpOff()
    {
        $this->showFrequencyPolygon = false;
        return $this;
    }

    public function crfpOn()
    {
        $this->showCumulativeRelativeFrequencyPolygon = true;
        return $this;
    }

    public function crfpOff()
    {
        $this->showCumulativeRelativeFrequencyPolygon = false;
        return $this;
    }

    public function frequencyOn()
    {
        $this->showFrequency = true;
        return $this;
    }

    public function frequencyOff()
    {
        $this->showFrequency = false;
        return $this;
    }

    public function create($filePath)
    {
        if (!is_string($filePath)) {
            return;
        }
        if (strlen($filePath) == 0) {
            return;
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
