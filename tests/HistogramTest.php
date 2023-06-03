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
        'fontPath',
        'fontSize',
        'fontColor',
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

    public function test_frame_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['x' => null, 'y' => 0.5, ],
            ['x' => true, 'y' => 0.5, ],
            ['x' => false, 'y' => 0.5, ],
            ['x' => -0.5, 'y' => 0.5, ],
            ['x' => 0, 'y' => 0.5, ],
            ['x' => 1.2, 'y' => 0.5, ],
            ['x' => [], 'y' => 0.5, ],
            ['x' => '', 'y' => 0.5, ],
            ['x' => '0.5', 'y' => 0.5, ],

            ['x' => 0.5, 'y' => null, ],
            ['x' => 0.5, 'y' => true, ],
            ['x' => 0.5, 'y' => false, ],
            ['x' => 0.5, 'y' => -0.5, ],
            ['x' => 0.5, 'y' => 0, ],
            ['x' => 0.5, 'y' => 1.2, ],
            ['x' => 0.5, 'y' => [], ],
            ['x' => 0.5, 'y' => '', ],
            ['x' => 0.5, 'y' => '0.5', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->frame($case['x'], $case['y']));
        }
    }

    public function test_frame_can_set_frame_ratio_correctly(): void
    {
        $hg = new Histogram();
        $x = $hg->getConfig('frameXRatio');
        $y = $hg->getConfig('frameYRatio');
        unset($hg);
        $cases = [
            ['x' => null, 'y' => null, 'expect' => ['x' => $x, 'y' => $y, ], ],
            ['x' => 0.5, 'y' => null, 'expect' => ['x' => $x, 'y' => $y, ], ],
            ['x' => null, 'y' => 0.5, 'expect' => ['x' => $x, 'y' => $y, ], ],
            ['x' => 0.2, 'y' => 0.3, 'expect' => ['x' => 0.2, 'y' => 0.3, ], ],
            ['x' => 1.0, 'y' => 1.0, 'expect' => ['x' => 1.0, 'y' => 1.0, ], ],
            ['x' => 0.0, 'y' => 0.0, 'expect' => ['x' => $x, 'y' => $y, ], ],
            ['x' => 1.1, 'y' => 1.1, 'expect' => ['x' => $x, 'y' => $y, ], ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->frame($case['x'], $case['y']);
            $this->assertSame($case['expect']['x'], $hg->getConfig('frameXRatio'));
            $this->assertSame($case['expect']['y'], $hg->getConfig('frameYRatio'));
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

    public function test_color_can_return_null_with_invalid_param(): void
    {
        $cases = [
            ['color' => null, ],
            ['color' => true, ],
            ['color' => false, ],
            ['color' => 0, ],
            ['color' => 1.2, ],
            ['color' => [], ],
            ['color' => '', ],
            ['color' => '0', ],
            ['color' => 'fff', ],
            ['color' => 'ffffff', ],
            ['color' => '#ff', ],
            ['color' => '#ffg', ],
            ['color' => '#ffff', ],
            ['color' => '#fffff', ],
            ['color' => '#fffffg', ],
            ['color' => '#fffffff', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->color($case['color']));
        }
    }

    public function test_color_can_set_color(): void
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('barBackgroundColor');
        $cases = [
            ['color' => null, 'expect' => $defaultColor, ],
            ['color' => true, 'expect' => $defaultColor, ],
            ['color' => false, 'expect' => $defaultColor, ],
            ['color' => 0, 'expect' => $defaultColor, ],
            ['color' => 1.2, 'expect' => $defaultColor, ],
            ['color' => [], 'expect' => $defaultColor, ],
            ['color' => '', 'expect' => $defaultColor, ],
            ['color' => 'fff', 'expect' => $defaultColor, ],
            ['color' => 'ffffff', 'expect' => $defaultColor, ],
            ['color' => '#000', 'expect' => '#000', ],
            ['color' => '#000000', 'expect' => '#000000', ],
        ];
        unset($hg);

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->color($case['color']);
            $this->assertSame($case['expect'], $hg->getConfig('barBackgroundColor'));
            unset($hg);
        }
    }

    public function test_border_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['width' => null, 'color' => null, ],
            ['width' => true, 'color' => null, ],
            ['width' => false, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 1, 'color' => null, ],
            ['width' => 1.2, 'color' => null, ],
            ['width' => [], 'color' => null, ],
            ['width' => '', 'color' => null, ],
            ['width' => 1, 'color' => null, ],
            ['width' => 1, 'color' => true, ],
            ['width' => 1, 'color' => false, ],
            ['width' => 1, 'color' => 0, ],
            ['width' => 1, 'color' => 1.2, ],
            ['width' => 1, 'color' => [], ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'black', ],
            ['width' => 1, 'color' => '000', ],
            ['width' => 1, 'color' => '000000', ],
            ['width' => 1, 'color' => '#00', ],
            ['width' => 1, 'color' => '#00g', ],
            ['width' => 1, 'color' => '#0000', ],
            ['width' => 1, 'color' => '#00000', ],
            ['width' => 1, 'color' => '#00000g', ],
            ['width' => 1, 'color' => '#0000000', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->border($case['width'], $case['color']));
        }
    }

    public function test_border_can_set_border(): void
    {
        $hg = new Histogram();
        $width = $hg->getConfig('barBorderWidth');
        $color = $hg->getConfig('barBorderColor');
        unset($hg);
        $cases = [
            ['width' => null, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => true, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => false, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 0, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => -1, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 1, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 1.2, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => [], 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => '', 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => true, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => false, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 0, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 1.2, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => [], 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 'black', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '000', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '000000', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#00g', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
            ['width' => 2, 'color' => '#00000g', 'expect' => ['width' => $width, 'color' => $color, ], ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->border($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('barBorderWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('barBorderColor'));
            unset($hg);
        }
    }

    public function test_fp_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['width' => null, 'color' => null, ],
            ['width' => true, 'color' => null, ],
            ['width' => false, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 1, 'color' => null, ],
            ['width' => 1.2, 'color' => null, ],
            ['width' => [], 'color' => null, ],
            ['width' => '', 'color' => null, ],
            ['width' => 1, 'color' => null, ],
            ['width' => 1, 'color' => true, ],
            ['width' => 1, 'color' => false, ],
            ['width' => 1, 'color' => 0, ],
            ['width' => 1, 'color' => 1.2, ],
            ['width' => 1, 'color' => [], ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'black', ],
            ['width' => 1, 'color' => '000', ],
            ['width' => 1, 'color' => '000000', ],
            ['width' => 1, 'color' => '#00', ],
            ['width' => 1, 'color' => '#00g', ],
            ['width' => 1, 'color' => '#0000', ],
            ['width' => 1, 'color' => '#00000', ],
            ['width' => 1, 'color' => '#00000g', ],
            ['width' => 1, 'color' => '#0000000', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->fp($case['width'], $case['color']));
        }
    }

    public function test_fp_can_set_width_and_color(): void
    {
        $hg = new Histogram();
        $width = $hg->getConfig('frequencyPolygonWidth');
        $color = $hg->getConfig('frequencyPolygonColor');
        unset($hg);
        $cases = [
            ['width' => null, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => true, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => false, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 0, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => -1, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 1, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 1.2, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => [], 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => '', 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => true, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => false, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 0, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 1.2, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => [], 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 'black', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '000', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '000000', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#00g', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
            ['width' => 2, 'color' => '#00000g', 'expect' => ['width' => $width, 'color' => $color, ], ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fp($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('frequencyPolygonWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('frequencyPolygonColor'));
            unset($hg);
        }
    }

    public function test_crfp_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['width' => null, 'color' => null, ],
            ['width' => true, 'color' => null, ],
            ['width' => false, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 1, 'color' => null, ],
            ['width' => 1.2, 'color' => null, ],
            ['width' => [], 'color' => null, ],
            ['width' => '', 'color' => null, ],
            ['width' => 1, 'color' => null, ],
            ['width' => 1, 'color' => true, ],
            ['width' => 1, 'color' => false, ],
            ['width' => 1, 'color' => 0, ],
            ['width' => 1, 'color' => 1.2, ],
            ['width' => 1, 'color' => [], ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'black', ],
            ['width' => 1, 'color' => '000', ],
            ['width' => 1, 'color' => '000000', ],
            ['width' => 1, 'color' => '#00', ],
            ['width' => 1, 'color' => '#00g', ],
            ['width' => 1, 'color' => '#0000', ],
            ['width' => 1, 'color' => '#00000', ],
            ['width' => 1, 'color' => '#00000g', ],
            ['width' => 1, 'color' => '#0000000', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->crfp($case['width'], $case['color']));
        }
    }

    public function test_crfp_can_set_width_and_color(): void
    {
        $hg = new Histogram();
        $width = $hg->getConfig('cumulativeRelativeFrequencyPolygonWidth');
        $color = $hg->getConfig('cumulativeRelativeFrequencyPolygonColor');
        unset($hg);
        $cases = [
            ['width' => null, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => true, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => false, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 0, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => -1, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 1, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 1.2, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => [], 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => '', 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => true, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => false, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 0, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 1.2, 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => [], 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => 'black', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '000', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '000000', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#00g', 'expect' => ['width' => $width, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
            ['width' => 2, 'color' => '#00000g', 'expect' => ['width' => $width, 'color' => $color, ], ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->crfp($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('cumulativeRelativeFrequencyPolygonWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('cumulativeRelativeFrequencyPolygonColor'));
            unset($hg);
        }
    }

    public function test_fontColor_can_set_color(): void
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('fontColor');
        $cases = [
            ['color' => null, 'expect' => $defaultColor, ],
            ['color' => true, 'expect' => $defaultColor, ],
            ['color' => false, 'expect' => $defaultColor, ],
            ['color' => 0, 'expect' => $defaultColor, ],
            ['color' => 1.2, 'expect' => $defaultColor, ],
            ['color' => [], 'expect' => $defaultColor, ],
            ['color' => '', 'expect' => $defaultColor, ],
            ['color' => 'fff', 'expect' => $defaultColor, ],
            ['color' => 'ffffff', 'expect' => $defaultColor, ],
            ['color' => '#000', 'expect' => '#000', ],
            ['color' => '#000000', 'expect' => '#000000', ],
        ];
        unset($hg);

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fontColor($case['color']);
            $this->assertSame($case['expect'], $hg->getConfig('fontColor'));
            unset($hg);
        }
    }

    public function test_fontPath_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['path' => null, ],
            ['path' => true, ],
            ['path' => false, ],
            ['path' => 0, ],
            ['path' => 1.2, ],
            ['path' => [], ],
            ['path' => '', ],
            ['path' => '.ttf', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->fontPath($case['path']));
        }
    }

    public function test_fontPath_can_set_font_path_correctly(): void
    {
        $hg = new Histogram();
        $path = $hg->getConfig('fontPath');
        unset($hg);
        $cases = [
            ['path' => null, 'expect' => $path, ],
            ['path' => true, 'expect' => $path, ],
            ['path' => false, 'expect' => $path, ],
            ['path' => 0, 'expect' => $path, ],
            ['path' => 1.2, 'expect' => $path, ],
            ['path' => [], 'expect' => $path, ],
            ['path' => '', 'expect' => $path, ],
            ['path' => '.ttf', 'expect' => $path, ],
            ['path' => 'hogehoge.tty', 'expect' => $path, ],
            ['path' => 'examples/fonts/ipaexg.ttf', 'expect' => 'examples/fonts/ipaexg.ttf', ],
            ['path' => '/usr/share/fonts/truetype/ubuntu/Ubuntu-C.ttf', 'expect' => '/usr/share/fonts/truetype/ubuntu/Ubuntu-C.ttf', ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fontPath($case['path']);
            $this->assertSame($case['expect'], $hg->getConfig('fontPath'));
            unset($hg);
        }
    }

    public function test_fontSize_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['size' => null, ],
            ['size' => true, ],
            ['size' => false, ],
            ['size' => 0, ],
            ['size' => 1.2, ],
            ['size' => [], ],
            ['size' => '', ],
            ['size' => '12', ],
            ['size' => -1, ],
            ['size' => 5, ],
        ];

        $hg = new Histogram();
        foreach ($cases as $index => $case) {
            $this->assertNull($hg->fontSize($case['size']));
        }
    }

    public function test_fontSize_can_set_font_size_correctly(): void
    {
        $hg = new Histogram();
        $size = $hg->getConfig('fontSize');
        unset($hg);
        $cases = [
            ['size' => null, 'expect' => $size, ],
            ['size' => true, 'expect' => $size, ],
            ['size' => false, 'expect' => $size, ],
            ['size' => 0, 'expect' => $size, ],
            ['size' => 1.2, 'expect' => $size, ],
            ['size' => [], 'expect' => $size, ],
            ['size' => '', 'expect' => $size, ],
            ['size' => '12', 'expect' => $size, ],
            ['size' => -12, 'expect' => $size, ],
            ['size' => 5, 'expect' => $size, ],
            ['size' => 6, 'expect' => 6, ],
            ['size' => 32, 'expect' => 32, ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fontSize($case['size']);
            $this->assertSame($case['expect'], $hg->getConfig('fontSize'));
            unset($hg);
        }
    }

    public function test_fontColor_can_return_null_with_invalid_param(): void
    {
        $cases = [
            ['color' => null, ],
            ['color' => true, ],
            ['color' => false, ],
            ['color' => 0, ],
            ['color' => 1.2, ],
            ['color' => [], ],
            ['color' => '', ],
            ['color' => '0', ],
            ['color' => 'fff', ],
            ['color' => 'ffffff', ],
            ['color' => '#ff', ],
            ['color' => '#ffg', ],
            ['color' => '#ffff', ],
            ['color' => '#fffff', ],
            ['color' => '#fffffg', ],
            ['color' => '#fffffff', ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->assertNull($hg->fontColor($case['color']));
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
            ['color' => '#ffg', 'expect' => false, ],
            ['color' => '#fff', 'expect' => true, ],
            ['color' => '#000', 'expect' => true, ],
            ['color' => '#ffff', 'expect' => false, ],
            ['color' => '#0000', 'expect' => false, ],
            ['color' => '#fffff', 'expect' => false, ],
            ['color' => '#00000', 'expect' => false, ],
            ['color' => '#fffffg', 'expect' => false, ],
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
