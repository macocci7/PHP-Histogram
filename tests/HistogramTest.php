<?php declare(strict_types=1);

require('vendor/autoload.php');
require('src/Histogram.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpHistogram\Histogram;

final class HistogramTest extends TestCase
{
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
        'classColor',
        'fontPath',
        'fontSize',
    ];

    public function test_size_can_return_size_correctly(): void
    {
        $cases = [
            ['width' => null, 'height' => null, 'expect' => null],
            ['width' => true, 'height' => null, 'expect' => null],
            ['width' => false, 'height' => null, 'expect' => null],
            ['width' => '0', 'height' => null, 'expect' => null],
            ['width' => [], 'height' => null, 'expect' => null],
            ['width' => 1.2, 'height' => null, 'expect' => null],
            ['width' => 1, 'height' => null, 'expect' => null],
            ['width' => 100, 'height' => null, 'expect' => null],
            ['width' => null, 'height' => true, 'expect' => null],
            ['width' => null, 'height' => false, 'expect' => null],
            ['width' => null, 'height' => '0', 'expect' => null],
            ['width' => null, 'height' => [], 'expect' => null],
            ['width' => null, 'height' => 1.2, 'expect' => null],
            ['width' => null, 'height' => 1, 'expect' => null],
            ['width' => null, 'height' => 100, 'expect' => null],
            ['width' => 100, 'height' => 1, 'expect' => null],
            ['width' => 1, 'height' => 100, 'expect' => null],
            ['width' => 100, 'height' => 100, 'expect' => ['width' => 100, 'height' => 100]],
            ['width' => 200, 'height' => 300, 'expect' => ['width' => 200, 'height' => 300]],
        ];
        foreach ($cases as $index => $case) {
            $hg = new Histogram($case['width'], $case['height']);
            $this->assertSame($case['expect'], $hg->size());
            unset($hg);
        }
    }

    public function test_bgcolor_can_work_correctly(): void
    {
        $cases = [
            ['color' => null, 'expect' => '#ffffff'],
            ['color' => true, 'expect' => '#ffffff'],
            ['color' => false, 'expect' => '#ffffff'],
            ['color' => 0, 'expect' => '#ffffff'],
            ['color' => 1.2, 'expect' => '#ffffff'],
            ['color' => '', 'expect' => '#ffffff'],
            ['color' => [], 'expect' => '#ffffff'],
            ['color' => 'red', 'expect' => '#ffffff'],
            ['color' => '000000', 'expect' => '#ffffff'],
            ['color' => '#0000ff', 'expect' => '#0000ff'],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->bgcolor($case['color']);
            $this->assertSame($case['expect'], $hg->getConfig('canvasBackgroundColor'));
            unset($hg);
        }
    }

    public function test_axis_can_return_null_with_invalid_params(): void
    {
        $cases = [
            ['width' => null, 'color' => null, ],
            ['width' => true, 'color' => null, ],
            ['width' => false, 'color' => null, ],
            ['width' => 1.2, 'color' => null, ],
            ['width' => '1', 'color' => null, ],
            ['width' => [], 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => 1, 'color' => true, ],
            ['width' => 1, 'color' => false, ],
            ['width' => 1, 'color' => 1, ],
            ['width' => 1, 'color' => 1.2, ],
            ['width' => 1, 'color' => [], ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'red', ],
            ['width' => 1, 'color' => 'fff', ],
            ['width' => 1, 'color' => 'ffffff', ],
            ['width' => 1, 'color' => '#ff', ],
            ['width' => 1, 'color' => '#ffff', ],
            ['width' => 1, 'color' => '#fffff', ],
            ['width' => 1, 'color' => '#fffffff', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->axis($case['width'], $case['color']));
        }
    }

    public function test_axis_can_set_property(): void
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('axisColor');
        $cases = [
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $defaultColor], ],
            ['width' => 3, 'color' => null, 'expect' => ['width' => 3, 'color' => $defaultColor], ],
            ['width' => 4, 'color' => null, 'expect' => ['width' => 4, 'color' => $defaultColor], ],
            ['width' => 2, 'color' => '#fff', 'expect' => ['width' => 2, 'color' => '#fff'], ],
            ['width' => 3, 'color' => '#ffffff', 'expect' => ['width' => 3, 'color' => '#ffffff'], ],
        ];
        unset($hg);

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->axis($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('axisWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('axisColor'));
            unset($hg);
        }
    }

    public function test_grid_can_return_null_with_invalid_params(): void
    {
        $cases = [
            ['width' => null, 'color' => null, ],
            ['width' => true, 'color' => null, ],
            ['width' => false, 'color' => null, ],
            ['width' => 1.2, 'color' => null, ],
            ['width' => '1', 'color' => null, ],
            ['width' => [], 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => 1, 'color' => true, ],
            ['width' => 1, 'color' => false, ],
            ['width' => 1, 'color' => 1, ],
            ['width' => 1, 'color' => 1.2, ],
            ['width' => 1, 'color' => [], ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'red', ],
            ['width' => 1, 'color' => 'fff', ],
            ['width' => 1, 'color' => 'ffffff', ],
            ['width' => 1, 'color' => '#ff', ],
            ['width' => 1, 'color' => '#ffff', ],
            ['width' => 1, 'color' => '#fffff', ],
            ['width' => 1, 'color' => '#fffffff', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->grid($case['width'], $case['color']));
        }
    }

    public function test_grid_can_set_property(): void
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('gridColor');
        $cases = [
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $defaultColor], ],
            ['width' => 3, 'color' => null, 'expect' => ['width' => 3, 'color' => $defaultColor], ],
            ['width' => 4, 'color' => null, 'expect' => ['width' => 4, 'color' => $defaultColor], ],
            ['width' => 2, 'color' => '#fff', 'expect' => ['width' => 2, 'color' => '#fff'], ],
            ['width' => 3, 'color' => '#ffffff', 'expect' => ['width' => 3, 'color' => '#ffffff'], ],
        ];
        unset($hg);

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->grid($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('gridWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('gridColor'));
            unset($hg);
        }
    }

    public function test_isColorCode_can_judge_correctly(): void
    {
        $cases = [
            ['color' => null, 'expect' => false, ],
            ['color' => true, 'expect' => false, ],
            ['color' => false, 'expect' => false, ],
            ['color' => 0, 'expect' => false, ],
            ['color' => 1.2, 'expect' => false, ],
            ['color' => [], 'expect' => false, ],
            ['color' => '', 'expect' => false, ],
            ['color' => 'red', 'expect' => false, ],
            ['color' => 'ffffff', 'expect' => false, ],
            ['color' => '#ff', 'expect' => false, ],
            ['color' => '#00', 'expect' => false, ],
            ['color' => '#fff', 'expect' => true, ],
            ['color' => '#000', 'expect' => true, ],
            ['color' => '#ffff', 'expect' => false, ],
            ['color' => '#0000', 'expect' => false, ],
            ['color' => '#fffff', 'expect' => false, ],
            ['color' => '#00000', 'expect' => false, ],
            ['color' => '#ffffff', 'expect' => true, ],
            ['color' => '#000000', 'expect' => true, ],
            ['color' => '#f0f0f0', 'expect' => true, ],
            ['color' => '#0f0f0f', 'expect' => true, ],
            ['color' => '#fffffff', 'expect' => false, ],
            ['color' => '#0000000', 'expect' => false, ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'], $hg->isColorCode($case['color']));
        }
    }

    public function test_getConfig_can_get_config_correctly(): void
    {
        $hg = new Histogram();
        $config = $hg->getConfig();
        $this->assertSame($this->validConfig, array_keys($config));
        $this->assertNull($hg->getConfig(''));
        $this->assertNull($hg->getConfig('hoge'));
        foreach ($this->validConfig as $key) {
            $this->assertSame($config[$key], $hg->getConfig($key));
        }
    }
}
