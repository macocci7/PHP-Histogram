<?php

declare(strict_types=1);

namespace Macocci7\PhpHistogram;

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

    public function test_frame_can_throw_exception_with_invalid_parameter(): void
    {
        $cases = [
            ['x' => -0.5, 'y' => 0.5, ],
            ['x' => 0.0, 'y' => 0.5, ],
            ['x' => 1.1, 'y' => 0.5, ],
            ['x' => 0.5, 'y' => -0.5, ],
            ['x' => 0.5, 'y' => 0.0, ],
            ['x' => 0.5, 'y' => 1.1, ],
        ];
        $hg = new Histogram();

        foreach ($cases as $index => $case) {
            $this->expectException(\Exception::class);
            $hg->frame($case['x'], $case['y']);
        }
    }

    public function test_frame_can_set_frame_ratio_correctly(): void
    {
        $hg = new Histogram();
        $x = $hg->getConfig('frameXRatio');
        $y = $hg->getConfig('frameYRatio');
        unset($hg);
        $cases = [
            ['x' => 0.2, 'y' => 0.3, 'expect' => ['x' => 0.2, 'y' => 0.3, ], ],
            ['x' => 1.0, 'y' => 1.0, 'expect' => ['x' => 1.0, 'y' => 1.0, ], ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->frame($case['x'], $case['y']);
            $this->assertSame($case['expect']['x'], $hg->getConfig('frameXRatio'));
            $this->assertSame($case['expect']['y'], $hg->getConfig('frameYRatio'));
            unset($hg);
        }
    }

    public function test_bgcolor_can_throw_exception_with_invalid_param(): void
    {
        $cases = [
            //['color' => ''],
            ['color' => 'red'],
            ['color' => 'fff'],
            ['color' => 'ffffff'],
            ['color' => '#ff'],
            ['color' => '#ffff'],
            ['color' => '#fffff'],
            ['color' => '#fffffff'],
        ];
        foreach ($cases as $case) {
            $hg = new Histogram();
            $this->expectException(\Exception::class);
            $hg->bgcolor($case['color']);
        }
    }

    public function test_bgcolor_can_work_correctly(): void
    {
        $cases = [
            ['color' => '#fff', 'expect' => '#fff'],
            ['color' => '#ff0000', 'expect' => '#ff0000'],
            ['color' => '#00ff00', 'expect' => '#00ff00'],
            ['color' => '#0000ff', 'expect' => '#0000ff'],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->bgcolor($case['color']);
            $this->assertSame($case['expect'], $hg->getConfig('canvasBackgroundColor'));
            unset($hg);
        }
    }

    public function test_axis_can_throw_exception_with_invalid_params(): void
    {
        $cases = [
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
            $this->expectException(\Exception::class);
            $hg->axis($case['width'], $case['color']);
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

    public function test_grid_can_throw_exception_with_invalid_params(): void
    {
        $cases = [
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
            $this->expectException(\Exception::class);
            $hg->grid($case['width'], $case['color']);
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

    public function test_color_can_throw_exception_with_invalid_param(): void
    {
        $cases = [
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
            $this->expectException(\Exception::class);
            $hg->color($case['color']);
        }
    }

    public function test_color_can_set_color(): void
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('barBackgroundColor');
        $cases = [
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

    public function test_border_can_throw_exception_with_invalid_parameter(): void
    {
        $cases = [
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 1, 'color' => null, ],
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
            $this->expectException(\Exception::class);
            $hg->border($case['width'], $case['color']);
        }
    }

    public function test_border_can_set_border(): void
    {
        $hg = new Histogram();
        $width = $hg->getConfig('barBorderWidth');
        $color = $hg->getConfig('barBorderColor');
        unset($hg);
        $cases = [
            ['width' => 1, 'color' => null, 'expect' => ['width' => 1, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
        ];

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->border($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('barBorderWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('barBorderColor'));
            unset($hg);
        }
    }

    public function test_fp_can_throw_exception_with_invalid_parameter(): void
    {
        $cases = [
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
            ['width' => 1, 'color' => null, ],
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
            $this->expectException(\Exception::class);
            $hg->fp($case['width'], $case['color']);
        }
    }

    public function test_fp_can_set_width_and_color(): void
    {
        $hg = new Histogram();
        $width = $hg->getConfig('frequencyPolygonWidth');
        $color = $hg->getConfig('frequencyPolygonColor');
        unset($hg);
        $cases = [
            ['width' => 1, 'color' => null, 'expect' => ['width' => 1, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
        ];
        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fp($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('frequencyPolygonWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('frequencyPolygonColor'));
            unset($hg);
        }
    }

    public function test_crfp_can_throw_exception_with_invalid_parameter(): void
    {
        $cases = [
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
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
            $this->expectException(\Exception::class);
            $hg->crfp($case['width'], $case['color']);
        }
    }

    public function test_crfp_can_set_width_and_color(): void
    {
        $hg = new Histogram();
        $width = $hg->getConfig('cumulativeRelativeFrequencyPolygonWidth');
        $color = $hg->getConfig('cumulativeRelativeFrequencyPolygonColor');
        unset($hg);
        $cases = [
            ['width' => 1, 'color' => null, 'expect' => ['width' => 1, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
        ];
        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->crfp($case['width'], $case['color']);
            $this->assertSame($case['expect']['width'], $hg->getConfig('cumulativeRelativeFrequencyPolygonWidth'));
            $this->assertSame($case['expect']['color'], $hg->getConfig('cumulativeRelativeFrequencyPolygonColor'));
            unset($hg);
        }
    }

    public function test_fontPath_can_throw_exception_with_invalid_parameter(): void
    {
        $cases = [
            ['path' => '', ],
            ['path' => '.ttf', ],
            ['path' => 'nonexistent.ttf', ],
            ['path' => 'example/class/fonts/dummy.otf', ],
        ];
        $hg = new Histogram();
        foreach ($cases as $index => $case) {
            $this->expectException(\Exception::class);
            $hg->fontPath($case['path']);
        }
    }

    public function test_fontPath_can_set_font_path_correctly(): void
    {
        $hg = new Histogram();
        $path = $hg->getConfig('fontPath');
        unset($hg);
        $cases = [
            ['path' => 'examples/fonts/ipaexg.ttf', 'expect' => 'examples/fonts/ipaexg.ttf', ],
            ['path' => 'examples/fonts/ipaexm.ttf', 'expect' => 'examples/fonts/ipaexm.ttf', ],
        ];
        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fontPath($case['path']);
            $this->assertSame($case['expect'], $hg->getConfig('fontPath'));
            unset($hg);
        }
    }

    public function test_fontSize_can_throw_exception_with_invalid_parameter(): void
    {
        $cases = [
            ['size' => -1, ],
            ['size' => 0, ],
            ['size' => 1, ],
            ['size' => 2, ],
            ['size' => 3, ],
            ['size' => 4, ],
            ['size' => 5, ],
        ];
        $hg = new Histogram();
        foreach ($cases as $index => $case) {
            $this->expectException(\Exception::class);
            $hg->fontSize($case['size']);
        }
    }

    public function test_fontSize_can_set_font_size_correctly(): void
    {
        $hg = new Histogram();
        $size = $hg->getConfig('fontSize');
        unset($hg);
        $cases = [
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

    public function test_fontColor_can_throw_exception_with_invalid_param(): void
    {
        $cases = [
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
            $this->expectException(\Exception::class);
            $hg->fontColor($case['color']);
        }
    }

    public function test_fontColor_can_set_color(): void
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('fontColor');
        $cases = [
            ['color' => '#ccc', 'expect' => '#ccc', ],
            ['color' => '#cccccc', 'expect' => '#cccccc', ],
        ];
        unset($hg);

        foreach ($cases as $index => $case) {
            $hg = new Histogram();
            $hg->fontColor($case['color']);
            $this->assertSame($case['expect'], $hg->getConfig('fontColor'));
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
