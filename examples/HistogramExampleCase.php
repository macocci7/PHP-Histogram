<?php
require_once('../vendor/autoload.php');
require('./class/CsvUtil.php');

use Macocci7\PhpHistogram\Histogram;
use Macocci7\CsvUtil;

$hg = new Histogram();
$config = [
    'canvasWidth' => 600,
    'canvasHeight' => 500,
];
$hg->ft->setClassRange(10);
$hg->ft->setData([0,5,10,15,20,22,24,26,28,30,33,36,39,40,45,50]);
$hg->configure($config);
$hg->create('img/HistogramExample01.png');
$hg->create('img/HistogramExample02.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => false, 'frequency' => false, ]);
$hg->create('img/HistogramExample03.png', ['bar' => true, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create('img/HistogramExample04.png', ['bar' => true, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => false, 'frequency' => true, ]);
$hg->create('img/HistogramExample05.png', ['bar' => true, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => true, 'frequency' => true, ]);
$hg->create('img/HistogramExample06.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create('img/HistogramExample07.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => false, 'frequency' => true, ]);
$hg->create('img/HistogramExample08.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => true, ]);
$hg->create('img/HistogramExample09.png', ['bar' => false, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => false, 'frequency' => false, ]);
$hg->create('img/HistogramExample10.png', ['bar' => false, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => false, 'frequency' => false, ]);
$hg->create('img/HistogramExample11.png', ['bar' => false, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create('img/HistogramExample12.png', ['bar' => false, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => false, 'frequency' => true, ]);
$hg->create('img/HistogramExample13.png', ['bar' => false, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create('img/HistogramExample14.png', ['bar' => false, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => true, ]);
$hg->create('img/HistogramExample15.png', ['bar' => true, 'frequencyPolygon' => true]);
$hg->create('img/HistogramExample16.png', ['bar' => true, 'cumulativeFrequencyPolygon' => true]);
$hg->create('img/HistogramExample17.png', ['bar' => true, 'frequency' => true]);
$hg->create('img/HistogramExample18.png', ['bar' => true]);
$hg->create('img/HistogramExample19.png', ['frequencyPolygon' => true]);
$hg->create('img/HistogramExample20.png', ['cumulativeFrequencyPolygon' => true]);
$hg->create('img/HistogramExample21.png', ['frequency' => true]);
