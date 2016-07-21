<?php

namespace Iamapen\ExcelFriendlyDataSet;

/**
 * Excel-friendly CSV DataSet. (multibyte locale)
 *
 * UTF-16(LE+BOM) converting to UTF-8.
 *
 * @author Yosuke Kushida <iamapen@studiopoppy.com>
 * @copyright 2010-2014
 */
class ExcelCsvDataSet extends CommentableCsvDataSet {
    /**
     * excel default is "\t"
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

        $fh      = fopen($csvFile, 'rb');
        fseek($fh, 2);  // after BOM

        // TODO chunk
        $tmpFp = fopen('php://temp', 'w+b');
        fwrite($tmpFp, mb_convert_encoding(stream_get_contents($fh), 'UTF-8', 'UTF-16LE'));
        rewind($tmpFp);

        $columns = $this->getCsvRow($tmpFp);

        if ($columns === FALSE)
        {
            throw new \InvalidArgumentException("Could not determine the headers from the given file {$csvFile}");
        }

        $metaData = new \PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
        $table    = new \PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

        while (($row = $this->getCsvRow($tmpFp)) !== FALSE)
        {
            $table->addRow(array_combine($columns, $row));
        }

        $this->tables[$tableName] = $table;
    }
}
