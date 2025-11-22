<?php

namespace Macocci7\PhpHistogram;

use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Typography\FontFactory;
use Macocci7\PhpFrequencyTable\FrequencyTable;
use Macocci7\PhpHistogram\Helpers\Config;
use Macocci7\PhpPlotter2d\Plotter as Plotter2d;
use Macocci7\PhpPlotter2d\Canvas;
use Macocci7\PhpPlotter2d\Transformer;

/**
 * Class for Plotting Histogram
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Plotter
{
    use Traits\JudgeTrait;
    use Traits\AttributeTrait;
    use Traits\StyleTrait;
    use Traits\VisibilityTrait;

    public FrequencyTable $ft;
    protected Canvas $canvas;
    protected Transformer $transformer;
    /**
     * @var array<string, int[]>    $viewport
     */
    protected array $viewport = [];
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
            'canvasBackgroundColor',
            'plotarea',
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
            'labelXOffsetX',
            'labelXOffsetY',
            'labelY',
            'labelYOffsetX',
            'labelYOffsetY',
            'caption',
            'captionOffsetX',
            'captionOffsetY',
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
        $this->setDefaultPlotarea();
        $this->setDefaultViewport();
        $this->adjustGridHeightPitch();
        $this->createCanvas();
    }

    /**
     * sets default viewport
     */
    private function setDefaultViewport(): void
    {
        $classes = $this->parsed['Classes'];
        if (count($classes) === 1) {
            $xMin = $classes[0]['bottom'];
            $xMax = $classes[0]['top'];
        } else {
            $xMin = array_shift($classes)['bottom'];
            $xMax = array_pop($classes)['top'];
        }
        $frequencies = $this->parsed['Frequencies'];
        $yMax = max($frequencies) + 1;
        $this->viewport = [
            'x' => [$xMin, $xMax],
            'y' => [0, $yMax],
        ];
    }

    /**
     * sets default plotarea
     */
    private function setDefaultPlotarea(): void
    {
        $plotarea = $this->plotarea;
        if (!array_key_exists('offset', $plotarea)) {
            $plotarea['offset'] = [
                (int) round(
                    $this->canvasWidth * (1 - $this->frameXRatio) / 2
                ),
                (int) round(
                    $this->canvasHeight * (1 - $this->frameYRatio) / 2
                ),
            ];
        }
        if (!array_key_exists('width', $plotarea)) {
            $plotarea['width'] = (int) round(
                $this->canvasWidth * $this->frameXRatio
            );
        }
        if (!array_key_exists('height', $plotarea)) {
            $plotarea['height'] = (int) round(
                $this->canvasHeight * $this->frameYRatio
            );
        }
        $this->plotarea = $plotarea;
    }

    /**
     * adjusts gridHeightPitch
     */
    private function adjustGridHeightPitch(): void
    {
        $yMax = max($this->parsed['Frequencies']) + 1;
        if ($this->gridHeightPitch < 0.2 * $yMax) {
            $this->gridHeightPitch = (int) (0.2 * $yMax);
        }
    }

    /**
     * creates canvas
     */
    private function createCanvas(): void
    {
        $this->canvas = Plotter2d::make(
            canvasSize: [
                'width' => $this->canvasWidth,
                'height' => $this->canvasHeight,
            ],
            viewport: $this->viewport,
            plotarea: $this->plotarea,
            backgroundColor: $this->canvasBackgroundColor,
        );
        $this->transformer = new Transformer(
            viewport: $this->viewport,
            plotarea: $this->plotarea,
        );
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
        $this->canvas->plotAxisX($this->axisWidth, $this->axisColor); // @phpstan-ignore-line
        $this->canvas->plotAxisY($this->axisWidth, $this->axisColor); // @phpstan-ignore-line
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
        // Horizontal Grids
        $this->canvas->plotGridHorizon( // @phpstan-ignore-line
            interval: $this->gridHeightPitch,
            width: $this->gridWidth,
            color: $this->gridColor,
        );
        // Vertical Line on the Left Edge
        $this->canvas->plotLine(
            x1: $this->viewport['x'][0],
            y1: $this->viewport['y'][0],
            x2: $this->viewport['x'][0],
            y2: $this->viewport['y'][1],
            width: $this->gridWidth,
            color: $this->gridColor,
        );
        // Vertical Line on the Right Edge
        $this->canvas->plotLine(
            x1: $this->viewport['x'][1],
            y1: $this->viewport['y'][0],
            x2: $this->viewport['x'][1],
            y2: $this->viewport['y'][1],
            width: $this->gridWidth + 2,
            color: $this->gridColor,
        );
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
        list($offsetX, $offsetY) = $this->canvas->getPlotarea()['offset'];
        $yMax = (int) $this->viewport['y'][1];
        for ($i = 0; $i <= $yMax; $i += $this->gridHeightPitch) {
            $coord = $this->transformer->getCoord(0, $i);
            $this->canvas->drawText(
                text: (string) $i,
                x: $offsetX - 8,
                y: $offsetY + $coord['y'],
                fontSize: $this->fontSize,
                fontPath: $this->fontPath,
                fontColor: $this->fontColor,
                align: 'right',
                valign: 'middle',
            );
        }
        return $this;
    }

    /**
     * plots a bar
     *
     * @param   array<string, int|float>    $class
     * @param   int                         $frequency
     * @return  self
     */
    private function plotBar(array $class, int $frequency)
    {
        $this->canvas->plotBox(
            x1: $class['bottom'],
            y1: $frequency,
            x2: $class['top'],
            y2: 0,
            backgroundColor: $this->barBackgroundColor,
            borderWidth: $this->barBorderWidth,
            borderColor: $this->barBorderColor,
        );
        return $this;
    }

    /**
     * plots bars
     * @return  self
     * @thrown  \Exception
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
            $this->plotBar($class, $this->parsed['Frequencies'][$index]);
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
        list($offsetX, $offsetY) = $this->plotarea['offset'];
        $coord = $this->transformer->getCoord($classes[0]['bottom'], 0);
        $this->canvas->drawText(
            text: (string) $classes[0]['bottom'],
            x: $offsetX,
            y: $offsetY + $coord['y'] + 4,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'top',
        );
        foreach ($classes as $class) {
            $coord = $this->transformer->getCoord($class['top'], 0);
            $this->canvas->drawText(
                text: (string) $class['top'],
                x: $offsetX + $coord['x'],
                y: $offsetY + $coord['y'] + 4,
                fontSize: $this->fontSize,
                fontPath: $this->fontPath,
                fontColor: $this->fontColor,
                align: 'center',
                valign: 'top',
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
        $classes = $this->parsed['Classes'];
        $count = count($frequencies);
        for ($i = 0; $i < $count - 1; $i++) {
            $this->canvas->plotLine(
                x1: ($classes[$i]['bottom'] + $classes[$i]['top']) / 2,
                y1: $frequencies[$i],
                x2: ($classes[$i + 1]['bottom'] + $classes[$i + 1]['top']) / 2,
                y2: $frequencies[$i + 1],
                width: $this->frequencyPolygonWidth,
                color: $this->frequencyPolygonColor,
            );
        }
        return $this;
    }

    /**
     * plots cumulative relative frequency polygon
     * @return  self
     * @thrown  \Exception
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
        $crfs = [-1 => 0];
        $classes = $this->parsed['Classes'];
        $yMax = $this->viewport['y'][1];
        foreach ($frequencies as $index => $frequency) {
            $crfs[] = $this->ft->getCumulativeRelativeFrequency($frequencies, $index);
            $this->canvas->plotLine(
                x1: $classes[$index]['bottom'],
                y1: $yMax * $crfs[$index - 1],
                x2: $classes[$index]['top'],
                y2: $yMax * $crfs[$index],
                width: $this->cumulativeRelativeFrequencyPolygonWidth,
                color: $this->cumulativeRelativeFrequencyPolygonColor,
            );
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
        $classes = $this->parsed['Classes'];
        list($offsetX, $offsetY) = $this->plotarea['offset'];
        foreach ($frequencies as $index => $frequency) {
            $coord = $this->transformer->getCoord(
                ($classes[$index]['bottom'] + $classes[$index]['top']) / 2,
                $frequency,
            );
            $this->canvas->drawText(
                text: (string) $frequency,
                x: $offsetX + $coord['x'],
                y: $offsetY + $coord['y'] - 6,
                fontSize: $this->fontSize,
                fontPath: $this->fontPath,
                fontColor: $this->fontColor,
                align: 'center',
                valign: 'bottom',
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
        $baseY = $this->plotarea['offset'][1] + $this->plotarea['height']; // @phpstan-ignore-line
        $x = (int) $this->canvasWidth / 2;
        $y = (int) (
            $baseY + ($this->canvasHeight - $this->plotarea['height']) / 3
        );
        $this->canvas->drawText(
            text: (string) $this->labelX,
            x: $x + $this->labelXOffsetX,
            y: $y + $this->labelXOffsetY,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'bottom',
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
        $height = (int) round(
            ($this->canvasWidth - $this->plotarea['width']) / 2
        );
        $x = (int) round($width / 2);
        $y = (int) round($height * 2 / 5);
        $this->canvas->drawText(
            text: (string) $this->labelY,
            x: $x,
            y: $y,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'middle',
            angle: 90,
            offsetX: $this->labelYOffsetX,
            offsetY: $this->labelYOffsetY,
            rotateAlign: 'left',
            rotateValign: 'bottom',
        );
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
        $x = (int) round($this->canvasWidth / 2);
        $y = (int) round(
            ($this->canvasHeight - $this->plotarea['height']) / 3
        );
        $this->canvas->drawText(
            (string) $this->caption,
            $x + $this->captionOffsetX,
            $y + $this->captionOffsetY,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'bottom',
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
        $this->canvas->save($filePath);
        return $this;
    }
}
