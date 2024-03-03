<?php

require('../vendor/autoload.php');
require('./class/CsvUtil.php');

use Macocci7\PhpHistogram\Histogram;
use Macocci7\CsvUtil;

$csvUtil = new CsvUtil();

$hg = new Histogram(360, 240);
$hg->setClassRange(5)
   ->frame(0.75, 0.65)
   ->fontSize(14)
   ->frequencyOn();

$dailyData = $csvUtil->getDailyData('csv/660271_data.csv');

echo "# Pitching speed (MPH)\n\n";
echo "## Pitcher:\n\n";
echo "[Ohtani Shohei](https://www.mlb.com/player/shohei-ohtani-660271)\n\n";
echo "## Data Source\n\n[savant](https://baseballsavant.mlb.com/)\n\n";
echo "## Dates\n\n";
foreach (array_keys($dailyData) as $key) {
    echo "- [" . $key . "](#" . $key . ")\n";
}
foreach ($dailyData as $key => $data) {
    echo "\n## " . $key . "\n\n";
    $histogramPath = 'img/HistogramOhtaniShohei' . $key . '.png';
    $hg->setData($data)
       ->create($histogramPath);
    echo "<details><summary>Properties</summary>\n\n";
    echo "|Property|Value|\n";
    echo "|:---|---:|\n";
    echo "|ClassRange|" . $hg->ft->getClassRange() . "|\n";
    echo "|Max|" . $hg->ft->getMax($data) . "|\n";
    echo "|Min|" . $hg->ft->getMin($data) . "|\n";
    echo "|DataRange|" . $hg->ft->getDataRange($data) . "|\n";
    echo "|Mode|" . $hg->ft->getMode() . "|\n";
    echo "|Mean|" . number_format($hg->ft->getMean(), 1, '.', ',') . "|\n";
    echo "|Median|" . $hg->ft->getMedian($data) . "|\n";
    echo "|FirstQuartile|" . $hg->ft->getFirstQuartile($data) . "|\n";
    echo "|ThirdQuartile|" . $hg->ft->getThirdQuartile($data) . "|\n";
    echo "|InterQuartileRange|" . $hg->ft->getInterQuartileRange($data) . "|\n";
    echo "|QuartileDeviation|" . $hg->ft->getQuartileDeviation($data) . "|\n";
    echo "</details>\n\n";
    echo "<details><summary>Frequency Table</summary>\n\n";
    echo $hg->ft->meanOn()->markdown();
    echo "</details>\n\n";
    echo "\n\n";
    echo "![Histogram:" . $key . "](" . $histogramPath . ")";
    echo "\n\n";
}
