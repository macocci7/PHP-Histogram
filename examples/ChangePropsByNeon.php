<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpHistogram\Histogram;

$hg = new Histogram();
$hg->setClassRange(5)
   ->setData([1, 5, 6, 10, 12, 14, 15, 16, 17, 18, 20, 24, 25])
   ->config('ChangePropsByNeon.neon')
   ->create('img/ChangePropsByNeon.png');
