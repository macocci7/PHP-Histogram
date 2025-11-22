<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpHistogram\Histogram;

$props = [
    'canvasWidth' => 600,
    'canvasHeight' => 400,
    'canvasBackgroundColor' => '#224499',
    'plotarea' => [
        'offset' => [90, 80],
        'width' => 420,
        'height' => 240,
        'backgroundColor' => null,
    ],
    'frameXRatio' => 0.5,
    'frameYRatio' => 0.4,
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
    'fontPath' => __DIR__ . '/fonts/ipaexg.ttf',
    'fontSize' => 24,
    'fontColor' => '#eeeeee',
    'showBar' => true,
    'showFrequencyPolygon' => true,
    'showCumulativeRelativeFrequencyPolygon' => true,
    'showFrequency' => true,
    'labelX' => 'Class (Items)',
    'labelXOffsetX' => 0,
    'labelXOffsetY' => 0,
    'labelY' => 'Frequency (People)',
    'labelYOffsetX' => 0,
    'labelYOffsetY' => 0,
    'caption' => 'Items Purchased / month（Feb 2024）',
    'captionOffsetX' => 0,
    'captionOffsetY' => 0,
];

$hg = new Histogram();
$hg->setClassRange(5)
   ->setData([1, 5, 6, 10, 12, 14, 15, 16, 17, 18, 20, 24, 25])
   ->config($props)
   ->create(__DIR__ . '/img/ChangePropsByArray.png');
