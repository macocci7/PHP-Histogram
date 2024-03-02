<?php

namespace Macocci7;

/**
 * class for csv operation
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */

class CsvUtil
{
    /**
     * constructor
     */
    public function __construct()
    {
    }

    /**
     * returns hash array grouped by $keyColumn
     * @param   list<string[]>  $csv
     * @param   string          $keyColumn
     * @param   string          $valueColumn
     * @return  array<string, list<string, string>>|null
     */
    public function groupBy($csv, $keyColumn, $valueColumn)
    {
        // CSV MUST INCLUDES COLUMN NAMES IN HEAD LINE
        $data = [...$csv];
        $head = array_shift($data);
        $indexKeyColumn = array_search($keyColumn, $head);
        $indexValueColumn = array_search($valueColumn, $head);
        if (!$indexKeyColumn || !$indexValueColumn) {
            return null;
        }
        $groupBy = [];
        foreach ($data as $row) {
            if (null == $row[$indexValueColumn]) {
                continue;
            }
            $key = $row[$indexKeyColumn];
            $groupBy[$key][] = $row[$indexValueColumn];
        }
        ksort($groupBy);
        return $groupBy;
    }

    /**
     * converts strings in array to integer values
     * @param   string[]   $strings
     * @return  int[]|null
     */
    public function convertString2IntegerInArray(array $strings)
    {
        foreach ($strings as $value) {
            if (!(is_numeric($value) || '' === $value)) {
                return null;
            }
        }
        $integers = [];
        foreach ($strings as $key => $value) {
            $integers[$key] = (int) $value;
        }
        return $integers;
    }

    /**
     * returns daily data
     * @param   string  $csvFileName
     * @return  array<string, int[]>
     * @thrown  \Exception
     */
    public function getDailyData(string $csvFileName)
    {
        if (!file_exists($csvFileName)) {
            throw new \Exception("CsvUtil::getDailyData(): '" . $csvFileName . "' does not exist.\n");
        }
        $csv = array_map('str_getcsv', file($csvFileName));
        $groupBy = $this->groupBy($csv, "game_date", "release_speed");
        foreach ($groupBy as $index => $row) {
            $groupBy[$index] = $this->convertString2IntegerInArray($row);
        }
        return $groupBy;
    }
}
