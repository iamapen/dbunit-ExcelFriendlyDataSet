<?php

namespace Iamapen\ExcelFriendlyDataSet\Database\DataSet;

/**
 * Ignore N columns from left.
 *
 * Usage for this is comment column.
 *
 * @author Yosuke Kushida <iamapen@studiopoppy.com>
 * @copyright 2010-2014
 */
class CommentableCsvDataSet extends \PHPUnit_Extensions_Database_DataSet_CsvDataSet
{

    /**
     * Ignore CSV columns
     * @var int
     */
    protected $ignoreColumnCount = 0;

    /**
     * Ignore CSV columns from left
     * @param int $count number of columns
     */
    public function setIgnoreColumnCount($count)
    {
        $this->ignoreColumnCount = $count;
    }

    /**
     * Returns a row from the csv file in an indexed array.
     *
     * @param resource $fh
     * @return array
     */
    protected function getCsvRow($fh)
    {
        if (version_compare(PHP_VERSION, '5.3.0', '>')) {
            $rows = fgetcsv($fh, NULL, $this->delimiter, $this->enclosure, $this->escape);
        } else {
            $rows = fgetcsv($fh, NULL, $this->delimiter, $this->enclosure);
        }

        if ($rows === false) {
            return false;
        }

        for ($i = 0; $i < $this->ignoreColumnCount; $i++) {
            array_shift($rows);
        }
        return $rows;
    }
}
