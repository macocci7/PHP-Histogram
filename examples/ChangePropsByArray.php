<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpHistogram\Histogram;

$props = [
    'canvasWidth' => 600,
    'canvasHeight' => 400,
    'canvasBackgroundColor' => '#224499',
    'frameXRatio' => 0.7,
    'frameYRatio' => 0.6,
    'axisColor' => '#999',
    'axisWidth' => 3,
    'gridColor' => '#eee',
    'gridWidth' => 1,
    'gridHeightPitch' => 1,
    'barBackgroundColor' => '#ffcc66',
    'barBorderColor' => '#ff6600',
    'barBorderWidth' => 2,
    'frequencyPolygonColor' => '#33cc00',
    'frequencyPolygonWidth' => 3,
    'cumulativeRelativeFrequencyPolygonColor' => '#ff5577',
    'cumulativeRelativeFrequencyPolygonWidth' => 7,
    'fontPath' => 'fonts/ipaexg.ttf',
    'fontSize' => 24,
    'fontColor' => '#eeeeee',
    'showBar' => true,
    'showFrequencyPolygon' => true,
    'showCumulativeRelativeFrequencyPolygon' => true,
    'showFrequency' => true,
    'labelX' => 'Class (Items)',
    'labelY' => 'Frequency (People)',
    'caption' => 'Items Purchased / month（Feb 2024）',
];

$hg = new Histogram();
$hg->setClassRange(5)
   ->setData([1, 5, 6, 10, 12, 14, 15, 16, 17, 18, 20, 24, 25])
   ->config($props)
   ->create('img/ChangePropsByArray.png');
