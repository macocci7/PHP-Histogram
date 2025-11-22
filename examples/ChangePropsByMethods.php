<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpHistogram\Histogram;

// Initialization
$hg = new Histogram();
$hg
   ->setClassRange(5)
   ->setData([1, 5, 6, 10, 12, 14, 15, 16, 17, 18, 20, 24, 25])

   // Changing Props By Methods

   // Canvas Size: ($width, $height) / Deafult: (400, 300)
   // 50 <= $width / 50 <= $height
   ->resize(600, 400)

   ->plotarea( // this takes precedence over 'frame()'
       offset: [120, 80],   // [x, y] in pix, default=[]
       width: 360, // width in pix, default=0
       height: 240,   // height in pix, default=0
       backgroundColor: null,  // null as transparent, default=null
   )

   // Ratio of the size of the plot area to the Canvas Size
   // frame($width, $height) / Default: (0.8, 0.7)
   // 0 < $width <= 1.0 / 0 < $height <= 1.0
   ->frame(0.6, 0.6)

   // Canvas Background Color
   // only #rgb and #rrggbb formats are supported.
   ->bgcolor('#3333cc')

   // Axis: width in pix and color
   ->axis(3, '#ffffff')

   // Grid: width in pix and color
   ->grid(1, '#cccccc')

   // Color of bars
   ->color('#99aaff')

   // Border of bars: width in pix and color
   ->border(4, '#0000ff')

   // Frequency Polygon: width in pix and color
   ->fp(4, '#00ff00')

   // Cumulative Relative Frequency Polygon
   ->crfp(3, '#ffff00')

   // Font Path
   // Note: Set the real path to the true type font (*.ttf)
   //       on your system.
   ->fontPath('/usr/share/fonts/opentype/ipafont-gothic/ipagp.ttf')

   // Font Size in pix
   ->fontSize(20)

   // Font Color
   ->fontColor('#ffff99')

   // Visibility of Histogram bars. barOff() is also available
   ->barOn()

   // Visibility of frequency polygon. fpOff() is also available
   ->fpOn()

   // Visibility of cumulative frequency polygon. crfpOff() is also available
   ->crfpOn()

   // Visibility of frequency. frequencyOff() is also available
   ->frequencyOn()

   // X Label
   ->labelX('Class (Items)')

   // Y Label
   ->labelY('Frequency (People)')

   // Caption
   ->caption('Items Purchased / month（Feb 2024）')

   // Save
   ->create(__DIR__ . '/img/ChangePropsByMethods.png');
