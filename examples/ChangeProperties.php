<?php

require('../vendor/autoload.php');

use Macocci7\PhpHistogram\Histogram;

$hg = new Histogram();
$hg->ft->setClassRange(5);
$hg->ft->setData([1, 5, 6, 10, 12, 14, 15, 16, 17, 18, 20, 24, 25]);
$hg->resize(600, 400)
   ->frame(0.6, 0.6)
   ->bgcolor('#3333cc')
   ->axis(3, '#ffffff')
   ->grid(1, '#cccccc')
   ->color('#99aaff')
   ->border(4, '#0000ff')
   ->fp(4, '#00ff00')
   ->crfp(3, '#ffff00')
   ->fontPath('/usr/share/fonts/truetype/ipafont-nonfree-uigothic/ipagui.ttf')
   ->fontSize(20)
   ->fontColor('#ffff99')
   ->barOn()
   ->fpOn()
   ->crfpOn()
   ->frequencyOn()
   ->labelX('Class (Items)')
   ->labelY('Frequency (People)')
   ->caption('Items Purchased / month（May 2023）')
   ->create('img/ChangeProperties.png');
