<?php declare(strict_types=1);

require('vendor/autoload.php');
require('src/Histogram.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpHistogram\Histogram;

final class HistogramTest extends TestCase
{
    private $configValidation = [
        'barHeigtPitch' => 'integer|min:1',
        'canvasWidth' => 'integer|min:100|max:1920',
        'canvasHeight' => 'integer|min:100|max:1080',
        'canvasBackgroundColor' => 'colorcode',
        'frameXRatio' => 'float|min:0.5|max:1.0',
        'frameYRatio'=> 'float|min:0.5|max:1.0',
        'axisColor' => 'colorcode',
        'axisWidth' => 'integer|min:1',
        'gridColor' => 'colorcode',
        'gridWidth' => 'integer|min:1',
        'gridHeightPitch' => 'integer|min:1',
        'barBackgroundColor' => 'colorcode',
        'barBorderColor' => 'colorcode',
        'barBorderWidth' => 'integer:min:1',
        'frequencyPolygonColor' => 'colorcode',
        'frequencyPolygonWidth' => 'integer|min:1',
        'cumulativeRelativeFrequencyPolygonColor' => 'colorcode',
        'cumulativeRelativeFrequencyPolygonWidth' => 'integer|min:1',
        'classColor' => 'colorcode',
        'fontPath' => 'file',
        'fontSize' => 'integer|min:6',
    ];
    private $config = [
        'barHeigtPitch' => 1,
        'canvasWidth' => 100,
        'canvasHeight' => 100,
        'canvasBackgroundColor' => '#ffffff',
        'frameXRatio' => 0.8,
        'frameYRatio'=> 0.8,
        'axisColor' => '#333333',
        'axisWidth' => 2,
        'gridColor' => '#999999',
        'gridWidth' => 1,
        'gridHeightPitch' => 1,
        'barBackgroundColor' => '#0000ff',
        'barBorderColor' => '#ccccff',
        'barBorderWidth' => 1,
        'frequencyPolygonColor' => '#ff0000',
        'frequencyPolygonWidth' => 2,
        'cumulativeRelativeFrequencyPolygonColor' => '#99ffff',
        'cumulativeRelativeFrequencyPolygonWidth' => 2,
        'classColor' => '#333333',
        'fontPath' => 'examples/fonts/ipaexg.ttf',
        'fontSize' => 16,
    ];

    public function test_getValidConfig_can_work_correctly(): void
    {
        $hg = new Histogram();
        $this->assertSame($this->config, $hg->getValidConfig($this->config));
    }

    public function test_validate_config_can_work()
    {
        $hg = new Histogram();
        foreach ($this->config as $key => $value) {
            $this->assertTrue($hg->validateConfig($key, $value));
        }
    }

    public function test_setConfigValidationWarning_can_work(): void
    {
        $warnings = [
            'barHeigtPitch' => ['integer' => 'hoge is not integer.', 'min:1' => '0 is less than 1.', ],
            'canvasWidth' => ['integer' => 'width is not integer', 'min:100' => '10 is less than 100.', 'max:1920' => '2000 is greater than 1920', ],
            'canvasHeight' => ['integer' => 'height is not integer', 'min:100' => '20 is less than 100', 'max:1080' => '1500 is greater than 1080', ],
            ];
        $hg = new Histogram();
        foreach ($warnings as $key => $warning) {
            foreach ($warning as $rule => $message) {
                $hg->setConfigValidationWarning($key, $rule, $message);
            }
        }
        $this->assertSame($warnings, $hg->getConfigValidationWarning());
    }

    public function test_setConfigValidationError_can_work(): void
    {
        $warnings = [
            'barHeigtPitch' => ['integer' => 'hoge is not integer.', 'min:1' => '0 is less than 1.', ],
            'canvasWidth' => ['integer' => 'width is not integer', 'min:100' => '10 is less than 100.', 'max:1920' => '2000 is greater than 1920', ],
            'canvasHeight' => ['integer' => 'height is not integer', 'min:100' => '20 is less than 100', 'max:1080' => '1500 is greater than 1080', ],
            ];
        $hg = new Histogram();
        foreach ($warnings as $key => $warning) {
            foreach ($warning as $rule => $message) {
                $hg->setConfigValidationError($key, $rule, $message);
            }
        }
        $this->assertSame($warnings, $hg->getConfigValidationError());
    }

    public function test_configure_can_work(): void
    {
        $hg = new Histogram();
        $hg->configure($this->config);
        $this->assertSame($this->config, $hg->getConfig());
    }
}
