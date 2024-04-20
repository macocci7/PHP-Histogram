<?php

namespace Macocci7\PhpHistogram;

use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;
use Macocci7\PhpFrequencyTable\FrequencyTable;
use Macocci7\PhpHistogram\Helpers\Config;

/**
 * Class for Plotting Histogram
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Plotter
{
    use Traits\JudgeTrait;
    use Traits\AttributeTrait;
    use Traits\StyleTrait;
    use Traits\VisibilityTrait;

    public FrequencyTable $ft;
    protected string $imageDriver;
    protected ImageManager $imageManager;
    protected ImageInterface $image;
    protected int|float $gridHeightPitch;
    protected int $barWidth;
    protected int|float $barHeightPitch;
    protected int $barMaxValue;
    protected int $barMinValue;
    protected int $baseX;
    protected int $baseY;
    /**
     * @var array<mixed>    $parsed = []
     */
    protected array $parsed = [];
    /**
     * @var array<string, array<string, string>>    $validConfig
     */
    protected array $validConfig;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->loadConf();
        $this->imageManager = ImageManager::{$this->imageDriver}();
        $this->ft = new FrequencyTable();
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'imageDriver',
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
            'showBar',
            'showGrid',
            'showGridValues',
            'showAxis',
            'showFrequencyPolygon',
            'showCumulativeRelativeFrequencyPolygon',
            'showFrequency',
            'labelX',
            'labelY',
            'caption',
            'validConfig',
        ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop);
        }
    }

    /**
     * sets properties
     * @return  void
     */
    private function setProperties()
    {
        $this->parsed = $this->ft->parse();
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 2);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
        $this->barMaxValue = max($this->parsed['Frequencies']) + 1;
        $this->barMinValue = 0;
        $this->barWidth = (int) ($this->canvasWidth * $this->frameXRatio / count($this->parsed['Classes']));
        $this->barHeightPitch = $this->canvasHeight * $this->frameYRatio / $this->barMaxValue;
        if ($this->gridHeightPitch < 0.2 * $this->barMaxValue) {
            $this->gridHeightPitch = (int) (0.2 * $this->barMaxValue);
        }
        $this->image = $this->imageManager->create($this->canvasWidth, $this->canvasHeight);
        if ($this->isColorCode($this->canvasBackgroundColor)) {
            $this->image = $this->image->fill($this->canvasBackgroundColor);
        }
    }

    /**
     * returns position of horizontal axis
     * @return  int[]
     */
    private function getHorizontalAxisPosition()
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
     * @return  int[]
     */
    private function getVerticalAxisPosition()
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
     * @return  self
     */
    private function plotAxis()
    {
        if (!$this->showAxis) {
            return $this;
        }
        list($x1, $y1, $x2, $y2) = $this->getHorizontalAxisPosition();
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->axisColor);
                $line->width($this->axisWidth);
            }
        );
        list($x1, $y1, $x2, $y2) = $this->getVerticalAxisPosition();
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->axisColor);
                $line->width($this->axisWidth);
            }
        );
        return $this;
    }

    /**
     * plots grids
     * @return  self
     */
    private function plotGrids()
    {
        if (!$this->showGrid) {
            return $this;
        }
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x1 = $this->baseX;
            $y1 = $this->baseY - $i * $this->barHeightPitch;
            $x2 = (int) ($this->canvasWidth * (1 + $this->frameXRatio) / 2);
            $y2 = $y1;
            $this->image->drawLine(
                function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                    $line->from($x1, $y1);
                    $line->to($x2, $y2);
                    $line->color($this->gridColor);
                    $line->width($this->gridWidth);
                }
            );
            $x1 = (int) ($this->canvasWidth * (1 + $this->frameXRatio) / 2);
            $y1 = $this->baseY - $this->barMaxValue * $this->barHeightPitch;
            $x2 = $x1;
            $y2 = $this->baseY;
            $this->image->drawLine(
                function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                    $line->from($x1, $y1);
                    $line->to($x2, $y2);
                    $line->color($this->gridColor);
                    $line->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots grid values
     * @return  self
     */
    private function plotGridValues()
    {
        if (!$this->showGridValues) {
            return $this;
        }
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x = (int) ($this->baseX - $this->fontSize * 1.1);
            $y = (int) ($this->baseY - $i * $this->barHeightPitch + $this->fontSize * 0.4);
            $this->image->text(
                (string) $i,
                $x,
                $y,
                function (FontFactory $font) {
                    $font->filename($this->fontPath);
                    $font->color($this->fontColor);
                    $font->size($this->fontSize);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
        return $this;
    }

    /**
     * returns position of the bar
     * @param   int $frequency
     * @param   int $index
     * @return  int[]
     */
    private function getBarPosition(int $frequency, int $index)
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
     * @return  self
     * @thrown  \Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function plotBars()
    {
        if (!$this->showBar) {
            return $this;
        }
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
            list($x1, $y1, $x2, $y2) = $this->getBarPosition($frequencies[$index], $index);
            $this->image->drawRectangle(
                $x1,
                $y1,
                function (RectangleFactory $rectangle) use ($x1, $y1, $x2, $y2) {
                    $rectangle->size($x2 - $x1, $y2 - $y1);
                    $rectangle->background($this->barBackgroundColor);
                    $rectangle->border($this->barBorderColor, $this->barBorderWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots classes
     * @return  self
     * @thrown  \Exception
     */
    private function plotClasses()
    {
        if (!array_key_exists('Classes', $this->parsed)) {
            throw new \Exception("Classes not found.");
        }
        $classes = $this->parsed['Classes'];
        $x = $this->baseX;
        $y = (int) ($this->baseY + $this->fontSize * 1.2);
        $this->image->text(
            $classes[0]['bottom'],
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        foreach ($classes as $index => $class) {
            $x = $this->baseX + ($index + 1) * $this->barWidth;
            $y = (int) ($this->baseY + $this->fontSize * 1.2);
            $this->image->text(
                $class['top'],
                $x,
                $y,
                function (FontFactory $font) {
                    $font->filename($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
        return $this;
    }

    /**
     * plots frequency polygon
     * @return  self
     * @thrown  \Exception
     */
    private function plotFrequencyPolygon()
    {
        if (!$this->showFrequencyPolygon) {
            return $this;
        }
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
        $count = count($frequencies);
        for ($i = 0; $i < $count - 1; $i++) {
            $x1 = (int) ($this->baseX + ($i + 0.5) * $this->barWidth);
            $y1 = $this->baseY - $frequencies[$i] * $this->barHeightPitch;
            $x2 = (int) ($this->baseX + ($i + 1.5) * $this->barWidth);
            $y2 = $this->baseY - $frequencies[$i + 1] * $this->barHeightPitch;
            $this->image->drawLine(
                function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                    $line->from($x1, $y1);
                    $line->to($x2, $y2);
                    $line->color($this->frequencyPolygonColor);
                    $line->width($this->frequencyPolygonWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots cumulative relative frequency polygon
     * @return  self
     * @thrown  \Exception
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function plotCumulativeRelativeFrequencyPolygon()
    {
        if (!$this->showCumulativeRelativeFrequencyPolygon) {
            return $this;
        }
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
            $y2 = (int) ($this->baseY - $ySpan * $crf);
            $this->image->drawLine(
                function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                    $line->from($x1, $y1);
                    $line->to($x2, $y2);
                    $line->color($this->cumulativeRelativeFrequencyPolygonColor);
                    $line->width($this->cumulativeRelativeFrequencyPolygonWidth);
                }
            );
            $x1 = $x2;
            $y1 = $y2;
        }
        return $this;
    }

    /**
     * plots frequencies
     * @return  self
     * @thrown  \Exception
     */
    private function plotFrequencies()
    {
        if (!$this->showFrequency) {
            return $this;
        }
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
            $x = (int) ($this->baseX + ($index + 0.5) * $this->barWidth);
            $y = (int) ($this->baseY - $frequency * $this->barHeightPitch - $this->fontSize * 0.6);
            $this->image->text(
                $frequency,
                $x,
                $y,
                function (FontFactory $font) {
                    $font->filename($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
        return $this;
    }

    /**
     * plots label of X
     * @return  self
     */
    private function plotLabelX()
    {
        if (!$this->labelX) {
            return $this;
        }
        $x = (int) $this->canvasWidth / 2;
        $y = (int) ($this->baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3);
        $this->image->text(
            (string) $this->labelX,
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
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
     * @return  self
     */
    private function plotLabelY()
    {
        if (!$this->labelY) {
            return $this;
        }
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        // background-color: tranceparent
        $image = $this->imageManager->create($width, $height);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $image->text(
            (string) $this->labelY,
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        $image->rotate(90);
        $this->image->place($image, 'left');
        return $this;
    }

    /**
     * plots caption
     * @return  self
     */
    private function plotCaption()
    {
        if (!$this->caption) {
            return $this;
        }
        $x = (int) ($this->canvasWidth / 2);
        $y = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 3);
        $this->image->text(
            (string) $this->caption,
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        return $this;
    }

    /**
     * creates a histogram image
     * @param   string  $filePath
     * @return  self
     * @thrown  \Exception
     */
    public function create(string $filePath)
    {
        if (strlen($filePath) === 0) {
            throw new \Exception("specify a file path to save image.");
        }
        $this->setProperties();
        $this->plotGrids();
        $this->plotGridValues();
        $this->plotBars();
        $this->plotAxis();
        $this->plotFrequencyPolygon();
        $this->plotCumulativeRelativeFrequencyPolygon();
        $this->plotClasses();
        $this->plotFrequencies();
        $this->plotLabelX();
        $this->plotLabelY();
        $this->plotCaption();
        $this->image->save($filePath);
        return $this;
    }
}
