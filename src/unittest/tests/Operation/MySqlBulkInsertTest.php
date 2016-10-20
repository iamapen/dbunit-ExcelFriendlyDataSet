<?php
use Iamapen\ExcelFriendlyDataSet\Database\Operation\MySqlBulkInsert;

require_once dirname(dirname(__FILE__)) . "/../fixtures" . '/DatabaseTestUtility.php';

/**
 * @since      File available since Release 1.0.0
 */
class Extensions_Database_Operation_OperationsTest extends PHPUnit_Extensions_Database_TestCase
{
    protected function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO/SQLite is required to run this test.');
        }

        parent::setUp();
    }

    public function getConnection()
    {
        return new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection(DBUnitTestUtility::getSQLiteMemoryDB(), 'sqlite');
    }

    public function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__).'/../../fixtures/XmlDataSets/OperationsTestFixture.xml');
    }

    public function testMysqlBulkInsert() {
        $operation = new MySqlBulkInsert();
        $operation->execute($this->getConnection(), new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__).'/../../fixtures/XmlDataSets/InsertOperationTest.xml'));
        $this->assertDataSetsEqual(new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__).'/../../fixtures/XmlDataSets/InsertOperationResult.xml'), $this->getConnection()->createDataSet());
    }
}
