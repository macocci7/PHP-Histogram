<?php
require('../vendor/autoload.php');

use Macocci7\PhpHistogram\Histogram;

$hg = new Histogram();
$hg->ft->setClassRange(5);
$hg->ft->setData([0,5,8,10,12,13,15,16,17,18,19,20,24]);
$hg->create('img/Histogram.png');
