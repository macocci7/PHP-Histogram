<?php

namespace Macocci7\PhpHistogram;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpFrequencyTable\FrequencyTable;

class Plotter
{
    public $ft;
    protected $image;
    protected $canvasWidth;
    protected $canvasHeight;
    protected $canvasBackgroundColor = '#ffffff';
    protected $frameXRatio = 0.8;
    protected $frameYRatio = 0.7;
    protected $axisColor = '#666666';
    protected $axisWidth = 2;
    protected $gridColor = '#333333';
    protected $gridWidth = 1;
    protected $gridHeightPitch;
    protected $barWidth;
    protected $barHeightPitch;
    protected $barBackgroundColor = '#0000ff';
    protected $barBorderColor = '#9999ff';
    protected $barBorderWidth = 1;
    protected $frequencyPolygonColor = '#ff0000';
    protected $frequencyPolygonWidth = 2;
    protected $cumulativeRelativeFrequencyPolygonColor = '#33ff66';
    protected $cumulativeRelativeFrequencyPolygonWidth = 2;
    protected $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //protected $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    protected $fontSize = 16;
    protected $fontColor = '#333333';
    protected $barMaxValue;
    protected $barMinValue;
    protected $baseX;
    protected $baseY;
    protected $parsed = [];
    protected $showBar = true;
    protected $showFrequencyPolygon = false;
    protected $showCumulativeRelativeFrequencyPolygon = false;
    protected $showFrequency = false;
    protected $labelX;
    protected $labelY;
    protected $caption;
    protected $validConfig = [
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
     */
    public function __construct()
    {
        Image::configure(['driver' => 'imagick']);
        $this->ft = new FrequencyTable();
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
        if ($this->labelX) {
            $this->plotLabelX();
        }
        if ($this->labelY) {
            $this->plotLabelY();
        }
        if ($this->caption) {
            $this->plotCaption();
        }
        $this->image->save($filePath);
        return $this;
    }
}
