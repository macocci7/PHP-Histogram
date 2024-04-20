<?php

namespace Macocci7\PhpHistogram\Traits;

trait VisibilityTrait
{
    /**
     * @var bool    $showBar
     */
    protected bool $showBar;

    /**
     * @var bool    $showFrequencyPolygon
     */
    protected bool $showFrequencyPolygon;

    /**
     * @var bool    $showCumulativeRelativeFrequencyPolygon
     */
    protected bool $showCumulativeRelativeFrequencyPolygon;

    /**
     * @var bool    $showFrequency
     */
    protected bool $showFrequency;

    /**
     * @var bool    $showGrid
     */
    protected bool $showGrid;

    /**
     * @var bool    $showGridValues
     */
    protected bool $showGridValues;

    /**
     * @var bool    $showAxis
     */
    protected bool $showAxis;

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
