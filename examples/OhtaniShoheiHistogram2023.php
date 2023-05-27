<?php

require('../vendor/autoload.php');
require('./class/CsvUtil.php');

use Macocci7\PhpHistogram\Histogram;
use Macocci7\CsvUtil;

//$csv = array_map('str_getcsv', file('csv/660271_data.csv'));
$csvUtil = new CsvUtil();

$hg = new Histogram();
$hg->ft->setClassRange(5);

$dailyData = $csvUtil->getDailyData('csv/660271_data.csv');
//$groupBy = groupBy($csv, "game_date", "release_speed");
echo "# Pitching speed (MPH)\n\n";
echo "## Pitcher:\n\n";
echo "[Ohtani Shohei](https://www.mlb.com/player/shohei-ohtani-660271)\n\n";
echo "## Data Source\n\n[savant](https://baseballsavant.mlb.com/)\n\n";
echo "## Dates\n\n";
foreach(array_keys($dailyData) as $key) {
    echo "- [" . $key . "](#" . $key . ")\n";
}
foreach ($dailyData as $key => $data) {
    //$d = convertString2IntegerInArray($data);
    echo "\n## " . $key . "\n\n";
    $hg->ft->setData($data);
    $histogramPath = 'img/HistogramOhtaniShohei'.$key.'.png';
    $hg->create($histogramPath, ['bar' => true, 'frequency' => true]);
    echo "<details><summary>Properties</summary>\n\n";
    echo "|Property|Value|\n";
    echo "|:---|---:|\n";
    echo "|ClassRange|" . $hg->ft->getClassRange() . "|\n";
    echo "|Max|" . $hg->ft->getMax($data) . "|\n";
    echo "|Min|" . $hg->ft->getMin($data) . "|\n";
    echo "|DataRange|" . $hg->ft->getDataRange($data) . "|\n";
    echo "|Mode|" . $hg->ft->getMode() . "|\n";
    echo "|Mean|" . number_format($hg->ft->getMean(),1,'.',',') . "|\n";
    echo "|Median|" . $hg->ft->getMedian($data) . "|\n";
    echo "|FirstQuartile|" . $hg->ft->getFirstQuartile($data) . "|\n";
    echo "|ThirdQuartile|" . $hg->ft->getThirdQuartile($data) . "|\n";
    echo "|InterQuartileRange|" . $hg->ft->getInterQuartileRange($data) . "|\n";
    echo "|QuartileDeviation|" . $hg->ft->getQuartileDeviation($data) . "|\n";
    echo "</details>\n\n";
    echo "<details><summary>Frequency Table</summary>\n\n";
    $hg->ft->show();
    echo "</details>\n\n";
    echo "\n\n";
    echo "![Histogram:".$key."](".$histogramPath.")";
    echo "\n\n";
}
