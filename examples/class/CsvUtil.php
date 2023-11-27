<?php

namespace Macocci7;

/**
 * Note: This code is written only for use in 'OutlierDetection.php'.
 */

class CsvUtil
{
    public function __construct()
    {
    }

    public function groupBy($csv, $keyColumn, $valueColumn)
    {
        // CSV MUST INCLUDES COLUMN NAMES IN HEAD LINE
        $data = [...$csv];
        $head = array_shift($data);
        $indexKeyColumn = array_search($keyColumn, $head);
        $indexValueColumn = array_search($valueColumn, $head);
        if (!$indexKeyColumn || !$indexValueColumn) {
            return;
        }
        $groupBy = [];
        foreach ($data as $index => $row) {
            if (null == $row[$indexValueColumn]) {
                continue;
            }
            $key = $row[$indexKeyColumn];
            $groupBy[$key][] = $row[$indexValueColumn];
        }
        ksort($groupBy);
        return $groupBy;
    }

    public function convertString2IntegerInArray($strings)
    {
        if (!is_array($strings)) {
            return;
        }
        foreach ($strings as $value) {
            if (!(is_numeric($value) || '' === $value)) {
                return;
            }
        }
        $integers = [];
        foreach ($strings as $key => $value) {
            $integers[$key] = (int) $value;
        }
        return $integers;
    }

    public function getDailyData($csvFileName)
    {
        if (!is_string($csvFileName)) {
            return;
        }
        if (!file_exists($csvFileName)) {
            echo "CsvUtil::getDailyData(): '" . $csvFileName . "' does not exist.\n";
        }
        $csv = array_map('str_getcsv', file($csvFileName));
        $groupBy = $this->groupBy($csv, "game_date", "release_speed");
        foreach ($groupBy as $index => $row) {
            $groupBy[$index] = $this->convertString2IntegerInArray($row);
        }
        return $groupBy;
    }
}
