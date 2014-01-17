<?php

namespace Iamapen\ExcelFriendlyDataSet;

stream_filter_register("convert.mbstring.*", "Stream_Filter_Mbstring");

/**
 * Excel friendly な CSV DataSet(DbUnit)
 * 
 * Excelで扱いやすいUTF-16(LE+BOM)のCSVをUTF-8に変換しながら取り込む。
 * @author Yosuke Kushida <iamapen@studiopoppy.com>
 * @copyright 2010
 */
class ExcelCsvDataSet extends \PHPUnit_Extensions_Database_DataSet_CsvDataSet {
    /**
     * @var string
     */
    protected $delimiter = "\t";

    /**
     * Creates a new CSV dataset
     *
     * You can pass in the parameters for how csv files will be read.
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($delimiter = "\t", $enclosure = '"', $escape = '"')
    {
        parent::__construct($delimiter, $enclosure, $escape);
    }

    /**
     * Adds a table to the dataset
     *
     * The table will be given the passed name. $csvFile should be a path to
     * a valid csv file (based on the arguments passed to the constructor.)
     *
     * @param string $tableName
     * @param string $csvFile
     */
    public function addTable($tableName, $csvFile)
    {
        if (!is_file($csvFile)) {
            throw new \InvalidArgumentException("Could not find csv file: {$csvFile}");
        }

        if (!is_readable($csvFile)) {
            throw new \InvalidArgumentException("Could not read csv file: {$csvFile}");
        }

        $fh      = fopen('php://filter/convert.mbstring.encoding.UTF-16LE:UTF-8/resource='.$csvFile, 'r');
        fseek($fh, 2);  // after BOM

        $columns = $this->getCsvRow($fh);

        if ($columns === FALSE)
        {
            throw new \InvalidArgumentException("Could not determine the headers from the given file {$csvFile}");
        }

        $metaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
        $table    = new \PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

        while (($row = $this->getCsvRow($fh)) !== FALSE)
        {
            $table->addRow(array_combine($columns, $row));
        }

        $this->tables[$tableName] = $table;
    }
}
