<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpHistogram\Histogram;

$hg = new Histogram();
$hg->config([
        'canvasBackgroundColor' => null,
        'barBackgroundColor' => '#3399cc',
        'barBorderColor' => '#0000ff',
        'barBorderWidth' => 2,
        'gridColor' => '#cc6666',
        'gridWidth' => 1,
        'axisColor' => '#aa6633',
        'fontColor' => '#882222',
        'caption' => 'Transparent Background',
   ])
   ->setClassRange(5)
   ->setData([ 1, 5, 8, 10, 11, 14, 16, 19, 20, ])
   ->create('img/TransparentBackground.png');
