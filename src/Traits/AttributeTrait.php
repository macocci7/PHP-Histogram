<?php

namespace Macocci7\PhpHistogram\Traits;

trait AttributeTrait
{
    protected int $CANVAS_WIDTH_LIMIT_LOWER;
    protected int $CANVAS_HEIGHT_LIMIT_LOWER;

    protected int $canvasWidth;
    protected int $canvasHeight;
    /**
     * @var array<string, int|int[]>    $plotarea
     */
    protected array $plotarea = [];
    protected float $frameXRatio;
    protected float $frameYRatio;
    protected string $labelX;
    protected int $labelXOffsetX;
    protected int $labelXOffsetY;
    protected string $labelY;
    protected int $labelYOffsetX;
    protected int $labelYOffsetY;
    protected string $caption;
    protected int $captionOffsetX;
    protected int $captionOffsetY;

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
     * sets plotarea
     *
     * @param   int[]       $offset = []
     * @param   int         $width = 0
     * @param   int         $height = 0
     * @param   string|null $backgroundColor = null
     * @return  self
     */
    public function plotarea(
        array $offset = [],
        int $width = 0,
        int $height = 0,
        string|null $backgroundColor = null,
    ) {
        if ($offset !== []) {
            $this->plotarea['offset'] = $offset;
        }
        if ($width > 0) {
            $this->plotarea['width'] = $width;
        }
        if ($height > 0) {
            $this->plotarea['height'] = $height;
        }
        if ($this->isColorCode($backgroundColor) || is_null($backgroundColor)) {
            $this->plotarea['backgroundColor'] = $backgroundColor;  // @phpstan-ignore-line
        }
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
     * sets label of X
     * @param   string  $label
     * @param   int     $offsetX = 0
     * @param   int     $offsetY = 0
     * @return  self
     */
    public function labelX(
        string $label,
        int $offsetX = 0,
        int $offsetY = 0,
    ) {
        $this->labelX = $label;
        $this->labelXOffsetX = $offsetX;
        $this->labelXOffsetY = $offsetY;
        return $this;
    }

    /**
     * sets label of Y
     * @param   string  $label
     * @param   int     $offsetX = 0
     * @param   int     $offsetY = 0
     * @return  self
     */
    public function labelY(
        string $label,
        int $offsetX = 0,
        int $offsetY = 0,
    ) {
        $this->labelY = $label;
        $this->labelYOffsetX = $offsetX;
        $this->labelYOffsetY = $offsetY;
        return $this;
    }

    /**
     * sets caption
     * @param   string  $caption
     * @param   int     $offsetX = 0
     * @param   int     $offsetY = 0
     * @return  self
     */
    public function caption(
        string $caption,
        int $offsetX = 0,
        int $offsetY = 0,
    ) {
        $this->caption = $caption;
        $this->captionOffsetX = $offsetX;
        $this->captionOffsetY = $offsetY;
        return $this;
    }
}
