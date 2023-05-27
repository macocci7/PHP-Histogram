<?php
namespace Macocci7\PhpHistogram;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpFrequencyTable\FrequencyTable;

class Histogram
{

    public $ft;
    private $image;
    private $canvasWidth = 400;
    private $canvasHeight = 300;
    private $canvasBackgroundColor = '#ffffff';
    private $frameXRatio = 0.8;
    private $frameYRatio = 0.7;
    private $axisColor = '#666666';
    private $axisWidth = 2;
    private $gridColor = '#333333';
    private $gridWidth = 1;
    private $gridHeightPitch;
    private $barWidth;
    private $barHeightPitch;
    private $barBackgroundColor = '#0000ff';
    private $barBorderColor = '#9999ff';
    private $barBorderWidth = 1;
    private $frequencyPolygonColor = '#ff0000';
    private $frequencyPolygonWidth = 2;
    private $cumulativeRelativeFrequencyPolygonColor = '#33ff66';
    private $cumulativeRelativeFrequencyPolygonWidth = 2;
    private $classColor = '#333333';
    private $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //private $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    private $fontSize = 16;
    private $barMaxValue;
    private $barMinValue;
    private $baseX;
    private $baseY;
    private $parsed = [];
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
    private $configValidationWarning = [];
    private $configValidationError = [];

    public function __construct()
    {
        Image::configure(['driver' => 'imagick']);
        $this->ft = new FrequencyTable();
    }

    public function getValidConfig($config)
    {
        $this->configValidationWarning = [];
        $this->configValidationError = [];
        if (!is_array($config)) {
            $this->configValidationError['isArray'] = '$config is not array.';
            return [];
        }
        if (empty($config)) {
            $this->configValidationWarning['count'] = '$config is empty.';
            return [];
        }
        $acceptableKeys = array_keys($this->configValidation);
        $validConfig = [];
        foreach ($config as $key => $value) {
            if (!in_array($key, $acceptableKeys)) continue;
            if ($this->validateConfig($key, $value)) $validConfig[$key] = $value;
        }
        return $validConfig;
    }

    public function validateConfig($key, $value)
    {
        if (!strlen($this->configValidation[$key])) return false;
        $conditions = explode('|',$this->configValidation[$key]);
        foreach ($conditions as $condition) {
            if (strcmp('file',$condition)===0) {
                if (!file_exists($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' does not exist.');
                    return false;
                }
                continue;
            }
            if (strcmp('integer',$condition)===0) {
                if (!is_int($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not integer.');
                    return false;
                }
                continue;
            }
            if (strcmp('float',$condition)===0) {
                if (!is_float($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not float.');
                    return false;
                }
                continue;
            }
            if (strcmp('string',$condition)===0) {
                if (!is_string($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not string.');
                    return false;
                }
                continue;
            }
            if (strcmp('colorcode',$condition)===0) {
                if (!preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not colorcode.');
                    return false;
                }
                continue;
            }
            if (str_starts_with($condition, 'min:')) {
                $min = substr($condition, 4);
                if (!is_numeric($min)) {
                    $this->setConfigValidationWarning($key, $condition, 'specified min condition ' . $min .' is not numeric.');
                    continue;
                }
                if ($value < (float) $min) {
                    $this->setConfigValidationError($key, $condition, $value . ' is less than ' . $min . '.');
                    return false;
                }
                continue;
            }
            if (str_starts_with($condition, 'max:')) {
                $max = substr($condition, 4);
                if (!is_numeric($max)) {
                    $this->setConfigValidationError($key, $condition, 'specified max condition ' . $max . ' is not numeric.');
                    continue;
                }
                if ($value > (float) $max) {
                    $this->setConfigValidationError($key, $condition, $value.' is greater than ' . $max . '.');
                    return false;
                }
                continue;
            }
        }
        return true;
    }

    public function setConfigValidationWarning($key, $rule, $message)
    {
        if (!array_key_exists($key, $this->configValidationWarning)) $this->configValidationWarning[$key] = [];
        $this->configValidationWarning[$key][$rule] = $message;
        return true;
    }

    public function setConfigValidationError($key, $rule, $message)
    {
        if (!array_key_exists($key, $this->configValidationError)) $this->configValidationError[$key] = [];
        $this->configValidationError[$key][$rule] = $message;
        return true;
    }

    public function getConfigValidationWarning()
    {
        return $this->configValidationWarning;
    }

    public function getConfigValidationError()
    {
        return $this->configValidationError;
    }

    public function configure($config)
    {
        foreach ($this->getValidConfig($config) as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }
    
    public function getConfig($key = null)
    {
        if (null === $key) {
            $config = [];
            foreach (array_keys($this->configValidation) as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (in_array($key, array_keys($this->configValidation))) return $this->{$key};
        return null;
    }

    public function getHorizontalAxisPosition()
    {
        return [
            (int) $this->baseX,
            (int) $this->baseY,
            (int) $this->canvasWidth * (1 + $this->frameXRatio) / 2,
            (int) $this->baseY,
        ];
    }

    public function getVerticalAxisPosition()
    {
        return [
            (int) $this->baseX,
            (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2,
            (int) $this->baseX,
            (int) $this->baseY,
        ];
    }

    public function setAxis()
    {
        list($x1,$y1,$x2,$y2) = $this->getHorizontalAxisPosition();
        $this->image->line($x1,$y1,$x2,$y2,function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
        list($x1,$y1,$x2,$y2) = $this->getVerticalAxisPosition();
        $this->image->line($x1,$y1,$x2,$y2,function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
    }

    public function setGrids()
    {
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x1 = $this->baseX;
            $y1 = $this->baseY - $i * $this->barHeightPitch;
            $x2 = $this->canvasWidth * (1 + $this->frameXRatio) / 2;
            $y2 = $y1;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->gridColor);
                $draw->width($this->gridWidth);
            });
            $x1 = $this->canvasWidth * (1 + $this->frameXRatio) / 2;
            $y1 = $this->baseY - $this->barMaxValue * $this->barHeightPitch;
            $x2 = $x1;
            $y2 = $this->baseY;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->gridColor);
                $draw->width($this->gridWidth);
            });
        }
    }

    public function setGridValues()
    {
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x = $this->baseX - $this->fontSize * 1.1;
            $y = $this->baseY - $i * $this->barHeightPitch + $this->fontSize * 0.4;
            $this->image->text($i,$x,$y, function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->classColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
    }

    public function getBarPosition($frequency, $index)
    {
        return [
            (int) ($this->baseX + $index * $this->barWidth),
            (int) ($this->baseY - $this->barHeightPitch * $frequency),
            (int) ($this->baseX + ($index + 1) * $this->barWidth),
            (int) $this->baseY,
        ];
    }

    public function setBars()
    {
        if (!array_key_exists('Classes', $this->parsed)) return;
        if (!array_key_exists('Frequencies', $this->parsed))  return;
        $classes = $this->parsed['Classes'];
        $frequencies = $this->parsed['Frequencies'];
        if (empty($classes) || empty($frequencies)) return;
        foreach ($classes as $index => $class) {
            list($x1,$y1,$x2,$y2) = $this->getBarPosition($frequencies[$index], $index);
            $this->image->rectangle($x1,$y1,$x2,$y2, function ($draw) {
                $draw->background($this->barBackgroundColor);
                $draw->border($this->barBorderWidth, $this->barBorderColor);
            });
        }
    }

    public function setClasses()
    {
        if (!array_key_exists('Classes', $this->parsed)) return;
        $classes = $this->parsed['Classes'];
        $x = $this->baseX;
        $y = $this->baseY + $this->fontSize * 1.2;
        $this->image->text($classes[0]['bottom'],$x,$y,function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->classColor);
            $font->align('center');
            $font->valign('bottom');
        });
        foreach ($classes as $index => $class) {
            $x = $this->baseX + ($index + 1) * $this->barWidth;
            $y = $this->baseY + $this->fontSize * 1.2;
            $this->image->text($class['top'],$x,$y,function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->classColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
    }

    public function setFrequencyPolygon()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) return;
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) return;
        if (count($frequencies) < 2) return;
        for ($i = 0; $i < count($frequencies) - 1; $i++) {
            $x1 = $this->baseX + ($i + 0.5) * $this->barWidth;
            $y1 = $this->baseY - $frequencies[$i] * $this->barHeightPitch;
            $x2 = $this->baseX + ($i + 1.5) * $this->barWidth;
            $y2 = $this->baseY - $frequencies[$i + 1] * $this->barHeightPitch;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->frequencyPolygonColor);
                $draw->width($this->frequencyPolygonWidth);
            });
        }
    }

    public function setCumulativeRelativeFrequencyPolygon()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) return;
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) return;
        if (count($frequencies) < 2) return;
        $x1 = $this->baseX;
        $y1 = $this->baseY;
        $yTop = $this->canvasHeight * (1 - $this->frameYRatio) / 2;
        $ySpan = $this->baseY - $yTop;
        foreach ($frequencies as $index => $frequency) {
            $crf = $this->ft->getCumulativeRelativeFrequency($frequencies, $index);
            $x2 = $this->baseX + ($index + 1) * $this->barWidth;
            $y2 = $this->baseY - $ySpan * $crf;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->cumulativeRelativeFrequencyPolygonColor);
                $draw->width($this->cumulativeRelativeFrequencyPolygonWidth);
            });
            $x1 = $x2;
            $y1 = $y2;
        }
    }

    public function setFrequencies()
    {
        if (!array_key_exists('Frequencies', $this->parsed)) return;
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) return;
        if (empty($frequencies)) return;
        foreach ($frequencies as $index => $frequency) {
            $x = $this->baseX + ($index + 0.5) * $this->barWidth;
            $y = $this->baseY - $frequency * $this->barHeightPitch - $this->fontSize * 0.6;
            $this->image->text($frequency, $x, $y, function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->classColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
    }

    private function setProperties()
    {
        $this->parsed = $this->ft->parse();
        $this->baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $this->baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        $this->barMaxValue = max($this->parsed['Frequencies']) + 1;
        $this->barMinValue = 0;
        $this->barWidth = $this->canvasWidth * $this->frameXRatio / count($this->parsed['Classes']);
        $this->barHeightPitch = $this->canvasHeight * $this->frameYRatio / $this->barMaxValue;
        $this->gridHeightPitch = 1;
        if ($this->gridHeightPitch < 0.2 * $this->barMaxValue)
            $this->gridHeightPitch = (int) (0.2 * $this->barMaxValue);
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
    }

    public function create($filePath, $option = [
        'bar' => true,
        'frequencyPolygon' => false,
        'cumulativeFrequencyPolygon' => false,
        'frequency' => false,
    ])
    {
        if (!is_string($filePath)) return;
        if (strlen($filePath) == 0) return;
        $this->setProperties();
        $this->setGrids();
        $this->setGridValues();
        if (array_key_exists('bar', $option))
            if ($option['bar']) $this->setBars();
        $this->setAxis();
        if (array_key_exists('frequencyPolygon', $option))
            if ($option['frequencyPolygon']) $this->setFrequencyPolygon();
        if (array_key_exists('cumulativeFrequencyPolygon', $option))
            if ($option['cumulativeFrequencyPolygon']) $this->setCumulativeRelativeFrequencyPolygon();
        $this->setClasses();
        if (array_key_exists('frequency', $option))
            if ($option['frequency']) $this->setFrequencies();
        $this->image->save($filePath);
    }
}
