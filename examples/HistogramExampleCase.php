<?php
require_once('../vendor/autoload.php');
require('./class/CsvUtil.php');

use Macocci7\PhpHistogram\Histogram;
use Macocci7\CsvUtil;

$hg = new Histogram(600, 500);
$hg->ft->setClassRange(10);
$hg->ft->setData([0, 5, 10, 15, 20, 22, 24, 26, 28, 30, 33, 36, 39, 40, 45, 50]);

// CASE:01
// bar:on
// frequencyPolygon:off
// cumulativeRelativeFrequencyPolygon:off
// frequency:off
$hg->create('img/HistogramExample01.png');

// CASE:02
// bar:on
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:off
// frequency:off
$hg
   ->barOn()
   ->fpOn()
   ->crfpOff()
   ->frequencyOff()
   ->create('img/HistogramExample02.png');

// CASE:03
// bar:on
// frequencyPolygon:off
// cumulativeRelativeFrequencyPolygon:on
// frequency:off
$hg
   ->barOn()
   ->fpOff()
   ->crfpOn()
   ->frequencyOff()
   ->create('img/HistogramExample03.png');

// CASE:04
// bar:on
// frequencyPolygon:off
// cumulativeRelativeFrequencyPolygon:off
// frequency:on
$hg
   ->barOn()
   ->fpOff()
   ->crfpOff()
   ->frequencyOn()
   ->create('img/HistogramExample04.png');

// CASE:05
// bar:on
// frequencyPolygon:off
// cumulativeRelativeFrequencyPolygon:on
// frequency:on
$hg
   ->barOn()
   ->fpOff()
   ->crfpOn()
   ->frequencyOn()
   ->create('img/HistogramExample05.png');

// CASE:06
// bar:on
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:on
// frequency:off
$hg
   ->barOn()
   ->fpOn()
   ->crfpOn()
   ->frequencyOff()
   ->create('img/HistogramExample06.png');

// CASE:07
// bar:on
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:off
// frequency:on
$hg
   ->barOn()
   ->fpOn()
   ->crfpOff()
   ->frequencyOn()
   ->create('img/HistogramExample07.png');

// CASE:08
// bar:on
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:on
// frequency:on
$hg
   ->barOn()
   ->fpOn()
   ->crfpOn()
   ->frequencyOn()
   ->create('img/HistogramExample08.png');

// CASE:09
// bar:off
// frequencyPolygon:off
// cumulativeFrequencyPolygon:off
// frequency:off
$hg
   ->barOff()
   ->fpOff()
   ->crfpOff()
   ->frequencyOff()
   ->create('img/HistogramExample09.png');

// CASE:10
// bar:off
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:off
// frequency:off
$hg
   ->barOff()
   ->fpOn()
   ->crfpOff()
   ->frequencyOff()
   ->create('img/HistogramExample10.png');

// CASE:11
// bar:off
// frequencyPolygon:off
// cumulativeRelativeFrequencyPolygon:on
// frequency:off
$hg
   ->barOff()
   ->fpOff()
   ->crfpOn()
   ->frequencyOff()
   ->create('img/HistogramExample11.png');

// CASE:12
// bar:off
// frequencyPolygon:off
// cumulativeRelativeFrequencyPolygon:off
// frequency:on
$hg
   ->barOff()
   ->fpOff()
   ->crfpOff()
   ->frequencyOn()
   ->create('img/HistogramExample12.png');

// CASE:13
// bar:off
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:on
// frequency:off
$hg
   ->barOff()
   ->fpOn()
   ->crfpOn()
   ->frequencyOff()
   ->create('img/HistogramExample13.png');

// CASE:14
// bar:off
// frequencyPolygon:on
// cumulativeRelativeFrequencyPolygon:on
// frequency:on
$hg
   ->barOff()
   ->fpOn()
   ->crfpOn()
   ->frequencyOn()
   ->create('img/HistogramExample14.png');
