<?php

namespace Macocci7\PhpHistogram\Traits;

trait VisibilityTrait
{
    protected bool $showBar;
    protected bool $showFrequencyPolygon;
    protected bool $showCumulativeRelativeFrequencyPolygon;
    protected bool $showFrequency;
    protected bool $showGrid;
    protected bool $showGridValues;
    protected bool $showAxis;

    /**
     * sets bar-visibility on
     */
    public function barOn(): self
    {
        $this->showBar = true;
        return $this;
    }

    /**
     * sets var-visibility off
     */
    public function barOff(): self
    {
        $this->showBar = false;
        return $this;
    }

    /**
     * sets frequency-polygon-visibility on
     */
    public function fpOn(): self
    {
        $this->showFrequencyPolygon = true;
        return $this;
    }

    /**
     * sets frequency-polygon-visibility off
     */
    public function fpOff(): self
    {
        $this->showFrequencyPolygon = false;
        return $this;
    }

    /**
     * sets cumulative-relative-frequency-polygon-visibility on
     */
    public function crfpOn(): self
    {
        $this->showCumulativeRelativeFrequencyPolygon = true;
        return $this;
    }

    /**
     * sets cumulative-relative-frequency-polygon-visibility off
     */
    public function crfpOff(): self
    {
        $this->showCumulativeRelativeFrequencyPolygon = false;
        return $this;
    }

    /**
     * sets frequency-visibility on
     */
    public function frequencyOn(): self
    {
        $this->showFrequency = true;
        return $this;
    }

    /**
     * sets frequency-visibility off
     */
    public function frequencyOff(): self
    {
        $this->showFrequency = false;
        return $this;
    }
}
