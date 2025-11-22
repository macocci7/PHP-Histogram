<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpHistogram\Histogram;

$hg = new Histogram();
$hg->setClassRange(5)
   ->setData([ 0, 5, 8, 10, 12, 13, 15, 16, 17, 18, 19, 20, 24, ])
   ->create(__DIR__ . '/img/HistogramBasicUsage.png');
