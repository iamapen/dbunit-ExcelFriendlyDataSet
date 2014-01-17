<?php
/**
 * テストデータを一気に投入するサンプル
 *
 * dbunit単体で利用可。
 */

require 'PATH/TO/autoload.php';

$host = '192.168.33.10';
$port = '3306';
$user = 'leleco';
$pw = 'leleco';
$dbname = 'leleco';

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $user, $pw);
$pdo->query('SET SESSION FOREIGN_KEY_CHECKS=0;');

$con = new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($pdo);
$csvDs = new Iamapen\ExcelFriendlyDataSet\ExcelCsvDataSet("\t", '"', '"');
$csvDs->setIgnoreColumnCount(1);
$csvDs->addTable('users', 'PATH/TO/users.csv');
$csvDs->addTable('posts', 'PATH/TO/csv/posts.csv');

// replace null
$ds = new PHPUnit_Extensions_Database_DataSet_ReplacementDataSet($csvDs);
$ds->addFullReplacement('<null>', null);

$op = PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT()->execute($con, $ds);
