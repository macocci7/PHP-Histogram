<?php

declare(strict_types=1);

namespace Macocci7\PhpHistogram;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Macocci7\PhpHistogram\Histogram;
use Nette\Neon\Neon;

final class HistogramTest extends TestCase
{
    private $validConfig = [
        'canvasWidth',
        'canvasHeight',
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
        'labelXOffsetX',
        'labelXOffsetY',
        'labelY',
        'labelYOffsetX',
        'labelYOffsetY',
        'caption',
        'captionOffsetX',
        'captionOffsetY',
    ];

    public static function provide_size_can_return_size_correctly(): array
    {
        return [
            ['width' => null, 'height' => null, 'expect' => ['width' => 400, 'height' => 300]],
            ['width' => 100, 'height' => 100, 'expect' => ['width' => 100, 'height' => 100]],
            ['width' => 200, 'height' => 300, 'expect' => ['width' => 200, 'height' => 300]],
        ];
    }

    #[DataProvider('provide_size_can_return_size_correctly')]
    public function test_size_can_return_size_correctly(int|null $width, int|null $height, array|null $expect): void
    {
        if (is_null($width) && is_null($height)) {
            $hg = new Histogram();
        } else {
            $hg = new Histogram($width, $height);
        }
        $this->assertSame($expect, $hg->size());
    }

    public static function provide_resize_can_throw_exception_with_invalid_params(): array
    {
        $path = __DIR__ . '/../conf/Histogram.neon';
        $conf = Neon::decodeFile($path);
        $lowerLimitWidth = $conf['CANVAS_WIDTH_LIMIT_LOWER'];
        $lowerLimitHeight = $conf['CANVAS_HEIGHT_LIMIT_LOWER'];
        $messageWidth = "width is below the lower limit " . $lowerLimitWidth;
        $messageHeight = "height is below the lower limit " . $lowerLimitHeight;
        return [
            [ 'width' => -100, 'height' => -100, 'message' => $messageWidth, ],
            [ 'width' => -100, 'height' => 100, 'message' => $messageWidth, ],
            [ 'width' => 100, 'height' => -100, 'message' => $messageHeight, ],
            [ 'width' => 0, 'height' => 0, 'message' => $messageWidth, ],
            [ 'width' => 100, 'height' => 0, 'message' => $messageHeight, ],
            [ 'width' => 0, 'height' => 100, 'message' => $messageWidth, ],
            [ 'width' => $lowerLimitWidth - 1, 'height' => $lowerLimitHeight + 100, 'message' => $messageWidth, ],
            [ 'width' => $lowerLimitWidth + 100, 'height' => $lowerLimitHeight - 1, 'message' => $messageHeight, ],
        ];
    }

    #[DataProvider('provide_resize_can_throw_exception_with_invalid_params')]
    public function test_resize_can_throw_exception_with_invalid_params(int $width, int $height, string $message): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);
        $hg->resize($width, $height);
    }

    public static function provide_frame_can_throw_exception_with_invalid_parameter(): array
    {
        return [
            ['x' => -0.5, 'y' => 0.5, ],
            ['x' => 0.0, 'y' => 0.5, ],
            ['x' => 1.1, 'y' => 0.5, ],
            ['x' => 0.5, 'y' => -0.5, ],
            ['x' => 0.5, 'y' => 0.0, ],
            ['x' => 0.5, 'y' => 1.1, ],
        ];
    }

    #[DataProvider('provide_frame_can_throw_exception_with_invalid_parameter')]
    public function test_frame_can_throw_exception_with_invalid_parameter(float $x, float $y): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->frame($x, $y);
    }

    public static function provide_frame_can_set_frame_ratio_correctly(): array
    {
        return [
            ['x' => 0.2, 'y' => 0.3, 'expect' => ['x' => 0.2, 'y' => 0.3, ], ],
            ['x' => 1.0, 'y' => 1.0, 'expect' => ['x' => 1.0, 'y' => 1.0, ], ],
        ];
    }

    #[DataProvider('provide_frame_can_set_frame_ratio_correctly')]
    public function test_frame_can_set_frame_ratio_correctly(float $x, float $y, array $expect): void
    {
        $hg = new Histogram();
        $hg->frame($x, $y);
        $this->assertSame($expect['x'], $hg->getConfig('frameXRatio'));
        $this->assertSame($expect['y'], $hg->getConfig('frameYRatio'));
    }

    public static function provide_bgcolor_can_throw_exception_with_invalid_param(): array
    {
        return [
            //['color' => ''],
            ['color' => 'red'],
            ['color' => 'fff'],
            ['color' => 'ffffff'],
            ['color' => '#ff'],
            ['color' => '#ffff'],
            ['color' => '#fffff'],
            ['color' => '#fffffff'],
        ];
    }

    #[DataProvider('provide_bgcolor_can_throw_exception_with_invalid_param')]
    public function test_bgcolor_can_throw_exception_with_invalid_param(string $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->bgcolor($color);
    }

    public static function provide_bgcolor_can_work_correctly(): array
    {
        return [
            ['color' => '#fff', 'expect' => '#fff'],
            ['color' => '#ff0000', 'expect' => '#ff0000'],
            ['color' => '#00ff00', 'expect' => '#00ff00'],
            ['color' => '#0000ff', 'expect' => '#0000ff'],
        ];
    }

    #[DataProvider('provide_bgcolor_can_work_correctly')]
    public function test_bgcolor_can_work_correctly(string $color, string $expect): void
    {
        $hg = new Histogram();
        $hg->bgcolor($color);
        $this->assertSame($expect, $hg->getConfig('canvasBackgroundColor'));
    }

    public static function provide_axis_can_throw_exception_with_invalid_params(): array
    {
        return [
            ['width' => -1, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'red', ],
            ['width' => 1, 'color' => 'fff', ],
            ['width' => 1, 'color' => 'ffffff', ],
            ['width' => 1, 'color' => '#ff', ],
            ['width' => 1, 'color' => '#ffff', ],
            ['width' => 1, 'color' => '#fffff', ],
            ['width' => 1, 'color' => '#fffffff', ],
        ];
    }

    #[DataProvider('provide_axis_can_throw_exception_with_invalid_params')]
    public function test_axis_can_throw_exception_with_invalid_params(int $width, mixed $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->axis($width, $color);
    }

    public static function provide_axis_can_set_property(): array
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('axisColor');
        return [
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $defaultColor], ],
            ['width' => 3, 'color' => null, 'expect' => ['width' => 3, 'color' => $defaultColor], ],
            ['width' => 4, 'color' => null, 'expect' => ['width' => 4, 'color' => $defaultColor], ],
            ['width' => 2, 'color' => '#fff', 'expect' => ['width' => 2, 'color' => '#fff'], ],
            ['width' => 3, 'color' => '#ffffff', 'expect' => ['width' => 3, 'color' => '#ffffff'], ],
        ];
    }

    #[DataProvider('provide_axis_can_set_property')]
    public function test_axis_can_set_property(int $width, string|null $color, array $expect): void
    {
        $hg = new Histogram();
        $hg->axis($width, $color);
        $this->assertSame($expect['width'], $hg->getConfig('axisWidth'));
        $this->assertSame($expect['color'], $hg->getConfig('axisColor'));
    }

    public static function provide_grid_can_throw_exception_with_invalid_params(): array
    {
        return [
            ['width' => -1, 'color' => null, ],
            ['width' => 0, 'color' => null, ],
            ['width' => 1, 'color' => '', ],
            ['width' => 1, 'color' => 'red', ],
            ['width' => 1, 'color' => 'fff', ],
            ['width' => 1, 'color' => 'ffffff', ],
            ['width' => 1, 'color' => '#ff', ],
            ['width' => 1, 'color' => '#ffff', ],
            ['width' => 1, 'color' => '#fffff', ],
            ['width' => 1, 'color' => '#fffffff', ],
        ];
    }

    #[DataProvider('provide_grid_can_throw_exception_with_invalid_params')]
    public function test_grid_can_throw_exception_with_invalid_params(int $width, string|null $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->grid($width, $color);
    }

    public static function provide_grid_can_set_property(): array
    {
        $hg = new Histogram();
        $defaultColor = $hg->getConfig('gridColor');
        return [
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $defaultColor], ],
            ['width' => 3, 'color' => null, 'expect' => ['width' => 3, 'color' => $defaultColor], ],
            ['width' => 4, 'color' => null, 'expect' => ['width' => 4, 'color' => $defaultColor], ],
            ['width' => 2, 'color' => '#fff', 'expect' => ['width' => 2, 'color' => '#fff'], ],
            ['width' => 3, 'color' => '#ffffff', 'expect' => ['width' => 3, 'color' => '#ffffff'], ],
        ];
    }

    #[DataProvider('provide_grid_can_set_property')]
    public function test_grid_can_set_property(int $width, string|null $color, array $expect): void
    {
        $hg = new Histogram();
        $hg->grid($width, $color);
        $this->assertSame($expect['width'], $hg->getConfig('gridWidth'));
        $this->assertSame($expect['color'], $hg->getConfig('gridColor'));
    }

    public static function provide_color_can_throw_exception_with_invalid_param(): array
    {
        return [
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
    }

    #[DataProvider('provide_color_can_throw_exception_with_invalid_param')]
    public function test_color_can_throw_exception_with_invalid_param(string $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->color($color);
    }

    public static function provide_color_can_set_color(): array
    {
        return [
            ['color' => '#000', 'expect' => '#000', ],
            ['color' => '#000000', 'expect' => '#000000', ],
        ];
    }

    #[DataProvider('provide_color_can_set_color')]
    public function test_color_can_set_color(string $color, string $expect): void
    {
        $hg = new Histogram();
        $hg->color($color);
        $this->assertSame($expect, $hg->getConfig('barBackgroundColor'));
    }

    public static function provide_border_can_throw_exception_with_invalid_parameter(): array
    {
        return [
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
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
    }

    #[DataProvider('provide_border_can_throw_exception_with_invalid_parameter')]
    public function test_border_can_throw_exception_with_invalid_parameter(int $width, string|null $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->border($width, $color);
    }

    public static function provide_border_can_set_border(): array
    {
        $hg = new Histogram();
        $color = $hg->getConfig('barBorderColor');
        return [
            ['width' => 1, 'color' => null, 'expect' => ['width' => 1, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
        ];
    }

    #[DataProvider('provide_border_can_set_border')]
    public function test_border_can_set_border(int $width, string|null $color, array $expect): void
    {
        $hg = new Histogram();
        $hg->border($width, $color);
        $this->assertSame($expect['width'], $hg->getConfig('barBorderWidth'));
        $this->assertSame($expect['color'], $hg->getConfig('barBorderColor'));
    }

    public static function provide_fp_can_throw_exception_with_invalid_parameter(): array
    {
        return [
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
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
    }

    #[DataProvider('provide_fp_can_throw_exception_with_invalid_parameter')]
    public function test_fp_can_throw_exception_with_invalid_parameter(int $width, string|null $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->fp($width, $color);
    }

    public static function provide_fp_can_set_width_and_color(): array
    {
        $hg = new Histogram();
        $color = $hg->getConfig('frequencyPolygonColor');
        return [
            ['width' => 1, 'color' => null, 'expect' => ['width' => 1, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
        ];
    }

    #[DataProvider('provide_fp_can_set_width_and_color')]
    public function test_fp_can_set_width_and_color(int $width, mixed $color, array $expect): void
    {
        $hg = new Histogram();
        $hg->fp($width, $color);
        $this->assertSame($expect['width'], $hg->getConfig('frequencyPolygonWidth'));
        $this->assertSame($expect['color'], $hg->getConfig('frequencyPolygonColor'));
    }

    public static function provide_crfp_can_throw_exception_with_invalid_parameter(): array
    {
        return [
            ['width' => 0, 'color' => null, ],
            ['width' => -1, 'color' => null, ],
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
    }

    #[DataProvider('provide_crfp_can_throw_exception_with_invalid_parameter')]
    public function test_crfp_can_throw_exception_with_invalid_parameter(int $width, string|null $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->crfp($width, $color);
    }

    public static function provide_crfp_can_set_width_and_color(): array
    {
        $hg = new Histogram();
        $color = $hg->getConfig('cumulativeRelativeFrequencyPolygonColor');
        return [
            ['width' => 1, 'color' => null, 'expect' => ['width' => 1, 'color' => $color, ], ],
            ['width' => 2, 'color' => null, 'expect' => ['width' => 2, 'color' => $color, ], ],
            ['width' => 2, 'color' => '#000', 'expect' => ['width' => 2, 'color' => '#000', ], ],
            ['width' => 2, 'color' => '#000000', 'expect' => ['width' => 2, 'color' => '#000000', ], ],
        ];
    }

    #[DataProvider('provide_crfp_can_set_width_and_color')]
    public function test_crfp_can_set_width_and_color(int $width, string|null $color, array $expect): void
    {
        $hg = new Histogram();
        $hg->crfp($width, $color);
        $this->assertSame($expect['width'], $hg->getConfig('cumulativeRelativeFrequencyPolygonWidth'));
        $this->assertSame($expect['color'], $hg->getConfig('cumulativeRelativeFrequencyPolygonColor'));
    }

    public static function provide_fontPath_can_throw_exception_with_invalid_parameter(): array
    {
        return [
            ['path' => '', ],
            ['path' => '.ttf', ],
            ['path' => 'nonexistent.ttf', ],
            ['path' => 'example/class/fonts/dummy.otf', ],
        ];
    }

    #[DataProvider('provide_fontPath_can_throw_exception_with_invalid_parameter')]
    public function test_fontPath_can_throw_exception_with_invalid_parameter(string $path): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->fontPath($path);
    }

    public static function provide_fontPath_can_set_font_path_correctly(): array
    {
        return [
            ['path' => 'examples/fonts/ipaexg.ttf', 'expect' => 'examples/fonts/ipaexg.ttf', ],
            ['path' => 'examples/fonts/ipaexm.ttf', 'expect' => 'examples/fonts/ipaexm.ttf', ],
        ];
    }

    #[DataProvider('provide_fontPath_can_set_font_path_correctly')]
    public function test_fontPath_can_set_font_path_correctly(string $path, string $expect): void
    {
        $hg = new Histogram();
        $hg->fontPath($path);
        $this->assertSame($expect, $hg->getConfig('fontPath'));
    }

    public static function provide_fontSize_can_throw_exception_with_invalid_parameter(): array
    {
        return [
            ['size' => -1, ],
            ['size' => 0, ],
            ['size' => 1, ],
            ['size' => 2, ],
            ['size' => 3, ],
            ['size' => 4, ],
            ['size' => 5, ],
        ];
    }

    #[DataProvider('provide_fontSize_can_throw_exception_with_invalid_parameter')]
    public function test_fontSize_can_throw_exception_with_invalid_parameter(int $size): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->fontSize($size);
    }

    public static function provide_fontSize_can_set_font_size_correctly(): array
    {
        return [
            ['size' => 6, 'expect' => 6, ],
            ['size' => 32, 'expect' => 32, ],
        ];
    }

    #[DataProvider('provide_fontSize_can_set_font_size_correctly')]
    public function test_fontSize_can_set_font_size_correctly(int $size, int $expect): void
    {
        $hg = new Histogram();
        $hg->fontSize($size);
        $this->assertSame($expect, $hg->getConfig('fontSize'));
    }

    public static function provide_fontColor_can_throw_exception_with_invalid_param(): array
    {
        return [
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
    }

    #[DataProvider('provide_fontColor_can_throw_exception_with_invalid_param')]
    public function test_fontColor_can_throw_exception_with_invalid_param(string $color): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $hg->fontColor($color);
    }

    public static function provide_fontColor_can_set_color(): array
    {
        return [
            ['color' => '#ccc', 'expect' => '#ccc', ],
            ['color' => '#cccccc', 'expect' => '#cccccc', ],
        ];
    }

    #[DataProvider('provide_fontColor_can_set_color')]
    public function test_fontColor_can_set_color(string $color, string $expect): void
    {
        $hg = new Histogram();
        $hg->fontColor($color);
        $this->assertSame($expect, $hg->getConfig('fontColor'));
    }

    public static function provide_config_can_throw_exception_with_invalid_param(): array
    {
        return [
            [ 'configResource' => '', 'message' => 'Specify valid filename.', ],
            [ 'configResource' => 'hoge', 'message' => 'Cannot read file hoge.', ],
            [ 'configResource' => 'hoge', 'message' => 'Cannot read file hoge.', ],
            [ 'configResource' => __DIR__ . '/InvalidUserConfig.neon', 'message' => "canvasHeight must be type of int.", ],
            [
                'configResource' => [ 'frameXRatio' => 0.9, 'frameYRatio' => '0.9', ],
                'message' => 'frameYRatio must be type of float.',
            ],
        ];
    }

    #[DataProvider('provide_config_can_throw_exception_with_invalid_param')]
    public function test_config_can_throw_exception_with_invalid_param(string|array $configResource, string $message): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($message);
        $hg->config($configResource);
    }

    public function test_config_can_set_config_from_file_correctly(): void
    {
        $path = __DIR__ . '/ValidUserConfig.neon';
        $userConf = Neon::decodeFile($path);
        $hg = new Histogram();
        $conf = $hg->config($path)->getConfig();
        foreach ($conf as $key => $value) {
            if (isset($userConf[$key])) {
                $this->assertSame($userConf[$key], $value);
            }
        }
    }

    public static function provide_config_can_set_config_from_array_correctly(): array
    {
        return [
            [
                'userConf' => [
                    'canvasWidth' => 2000,
                    'canvasHeight' => 1000,
                    'canvasBackgroundColor' => '#0000ff',
                    'frameXRatio' => 0.91,
                    'frameYRatio' => 0.92,
                    'axisColor' => null,
                    'axisWidth' => 4,
                    'gridColor' => '#ff0000',
                    'gridWidth' => 5,
                    'gridHeightPitch' => 6,
                    'barBackgroundColor' => '#00ff00',
                    'barBorderColor' => '#006600',
                    'barBorderWidth' => 2,
                    'frequencyPolygonColor' => '#ff9900',
                    'frequencyPolygonWidth' => 3,
                    'cumulativeRelativeFrequencyPolygonColor' => '#ff00ff',
                    'cumulativeRelativeFrequencyPolygonWidth' => 7,
                    'fontPath' => 'fonts/ipaexm.ttf',
                    'fontSize' => 24,
                    'fontColor' => '#999999',
                ]
            ],
        ];
    }

    #[DataProvider('provide_config_can_set_config_from_array_correctly')]
    public function test_config_can_set_config_from_array_correctly(array $userConf): void
    {
        $hg = new Histogram();
        $conf = $hg->config($userConf)->getConfig();
        foreach ($conf as $key => $value) {
            if (isset($userConf[$key])) {
                $this->assertSame($userConf[$key], $value);
            }
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

    public static function provide_setClassRange_can_throw_exception_with_invalid_param(): array
    {
        return [
            [ 'classRange' => -1, ],
            [ 'classRange' => 0, ],
            [ 'classRange' => -1.5, ],
            [ 'classRange' => 0.0, ],
        ];
    }

    #[DataProvider('provide_setClassRange_can_throw_exception_with_invalid_param')]
    public function test_setClassRange_can_throw_exception_with_invalid_param(int|float $classRange): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Class range must be a positive number.");
        $hg->setClassRange($classRange);
    }

    public static function provide_setClassRange_can_set_class_range_correctly(): array
    {
        return [
            [ 'classRange' => 0.01, ],
            [ 'classRange' => 0.1, ],
            [ 'classRange' => 1.0, ],
            [ 'classRange' => 1, ],
            [ 'classRange' => 1.5, ],
            [ 'classRange' => 2, ],
        ];
    }

    #[DataProvider('provide_setClassRange_can_set_class_range_correctly')]
    public function test_setClassRange_can_set_class_range_correctly(int|float $classRange): void
    {
        $hg = new Histogram();
        $this->assertSame($classRange, $hg->setClassRange($classRange)->ft->getClassRange());
    }

    public static function provide_setData_can_throw_exception_with_invalid_param(): array
    {
        return [
            [ 'data' => [], ],
            [ 'data' => [null], ],
            [ 'data' => [true], ],
            [ 'data' => [false], ],
            [ 'data' => [''], ],
            [ 'data' => [[]], ],
            [ 'data' => [ 1, '2', ], ],
        ];
    }

    #[DataProvider('provide_setData_can_throw_exception_with_invalid_param')]
    public function test_setData_can_throw_exception_with_invalid_param(array $data): void
    {
        $hg = new Histogram();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Invalid data. Expected: array<int|string, int|float>");
        $hg->setData($data);
    }

    public static function provide_setData_can_set_data_correctly(): array
    {
        return [
            [ 'data' => [0], ],
            [ 'data' => [ -1, -2, -3, ], ],
            [ 'data' => [ 5, 4, 3, ], ],
        ];
    }

    #[DataProvider('provide_setData_can_set_data_correctly')]
    public function test_setData_can_set_data_correctly(array $data): void
    {
        $hg = new Histogram();
        $this->assertSame($data, $hg->setData($data)->ft->getData());
    }
}
