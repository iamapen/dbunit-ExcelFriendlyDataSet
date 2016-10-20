<?php
namespace Iamapen\ExcelFriendlyDataSet\Database\Operation;

/**
 * Bulk Insert (only MySQL)
 *
 * いずれ、バルクのチャンクサイズを指定できるようにしたい
 * @version 0.0.1
 */
class MySqlBulkInsert implements \PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    protected $operationName = 'MYSQL_BULK_INSERT';

    protected function buildOperationQuery(\PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, \PHPUnit_Extensions_Database_DataSet_ITable $table, \PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, $rowCount)
    {
        $columnCount = count($table->getTableMetaData()->getColumns());

        if ($columnCount > 0) {
            $placeHolders = implode(', ', array_fill(0, $columnCount, '?'));

            $columns = '';
            foreach ($table->getTableMetaData()->getColumns() as $column) {
                $columns .= $connection->quoteSchemaObject($column) . ', ';
            }

            $columns = substr($columns, 0, -2);

            $query = "
                INSERT INTO {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
                ({$columns})
                VALUES
                ({$placeHolders})
            ";

            $bulk = '';
            for ($i=1; $i<$rowCount; $i++) {
                $bulk .= ",({$placeHolders})\n                ";
            }
            $query .= $bulk;

            return $query;
        } else {
            return FALSE;
        }
    }

    protected function disablePrimaryKeys(\PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, \PHPUnit_Extensions_Database_DataSet_ITable $table, \PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection)
    {
        if (count($databaseTableMetaData->getPrimaryKeys())) {
            return TRUE;
        }

        return FALSE;
    }



    protected function buildOperationArguments(\PHPUnit_Extensions_Database_DataSet_ITableMetaData $databaseTableMetaData, \PHPUnit_Extensions_Database_DataSet_ITable $table, $row)
    {
        $args = array();
        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }

    /**
     * @param \PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param \PHPUnit_Extensions_Database_DataSet_IDataSet       $dataSet
     */
    public function execute(\PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, \PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $dataSet->getIterator();

        foreach ($dsIterator as $table) {
            $rowCount = $table->getRowCount();

            if($rowCount == 0) continue;

            /* @var $table \PHPUnit_Extensions_Database_DataSet_ITable */
            $databaseTableMetaData = $databaseDataSet->getTableMetaData($table->getTableMetaData()->getTableName());

            $disablePrimaryKeys    = $this->disablePrimaryKeys($databaseTableMetaData, $table, $connection);

            if ($disablePrimaryKeys) {
                $connection->disablePrimaryKeys($databaseTableMetaData->getTableName());
            }


            $bulkI = 0;
            while ($bulkI < $rowCount) {
                $chunkLen = 0;
                $argsList = array();
                for ($i=0; $i<100; $i++) {
                    $rowNum = $bulkI + $i;
                    if ($rowNum >= $rowCount) {
                        break;
                    }
                    $argsList = array_merge($argsList, $this->buildOperationArguments($databaseTableMetaData, $table, $rowNum));
                    $chunkLen++;
                }

                $query = $this->buildOperationQuery($databaseTableMetaData, $table, $connection, $chunkLen);
                if ($query === FALSE) {
                    if ($table->getRowCount() > 0) {
                        throw new \PHPUnit_Extensions_Database_Operation_Exception($this->operationName, '', array(), $table, 'Rows requested for insert, but no columns provided!');
                    }
                    continue;
                }

                try {
                    $statement = $connection->getConnection()->prepare($query);
                    $statement->execute($argsList);
                }
                catch (\Exception $e) {
                    throw new \PHPUnit_Extensions_Database_Operation_Exception(
                        $this->operationName, $query, $argsList, $table, $e->getMessage()
                    );
                }

                $bulkI += $i;
            }

            if ($disablePrimaryKeys) {
                $connection->enablePrimaryKeys($databaseTableMetaData->getTableName());
            }
        }
    }
}
