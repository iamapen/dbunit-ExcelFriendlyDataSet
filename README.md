ExcelFriendlyDataSet
===============

phpunit3～4/dbunit 用の DataSet や Operation の詰め合わせ。

元々はExcelで扱いやすい UTF-16(LE+BOM) のCSVを phpunit/dbunit で使うための CsvDataSet がメインだったためこの名前になっているが、
今後はただの拡張の詰め合わせになる予定。

※phpunit5～7 へ対応したものは別プロジェクトになりました。  
今後はこちら [iamapen/commentable-csv-data-set](https://packagist.org/packages/iamapen/commentable-csv-data-set)


Install
=======


composer.json

    require-dev: {
      "iamapen/excel-friendly-data-set": ">=1.1.0"
    }

Usage
=====

## DataSet

### DataSet/CommentableDataSet
CSVの左n列をコメント列扱いとして、無視する(取り込まない) 機能を持つ CsvDataSet。  
テストデータに対してのコメント列として使える。

```php
$ds = new Iamapen\ExcelFriendlyDataSet\Database\DataSet\CommentableCsvDataSet(",");
$ds->setIgnoreColumnCount(1);    // 1列目を無視
```

CSVの例
```csv
最初の列はコメント,id,user_name
男性ユーザ,1,taro soramame
女性女性ユーザ,2,arare norimaki
1ヶ月以上ログインしてないユーザ,3,akane kimidori
削除されるユーザ,4,gajira norimaki
```


### DataSet/ExcelCsvDataSet

最初に作成した DataSet。  
Excelで扱いやすい UTF-16-LE(+BOM) のCSVを UTF-8 に変換しながら取り込むもの。  
現在は出番はないと思われる。

2007年作成当時は UTF-8 の CSVを満足に編集できるソフトが少ないという経緯で作られた。  
しかし現在は LibreOffice-Calc 等のエディタでUTF8のCSVを容易に編集できるため、
わざわざExcel用に UTF-16 で保存しておくこともない。


以下、旧 README のまま。

Excelでは UTF-8 のcsvをまともに編集(とくに保存)ができないが、
UTF-16-LE(+BOM) にすれば「D&Dで開く」「Ctrl+S」で保存など比較的自然に編集でき、
テストデータにUnicode文字も使える。

代わりにテストコード内で UTF-16 -> UTF-8 変換が必要になるので、これを行う。


なおExcelからは"Unicode", sakuraエディタでは"Unicode", vimでは"utf16le", mbstringでは"UTF-16"で扱える。
新規CSV作成時はテキストエディタでUTF-16のファイルを作成してそれをExcelにD&Dするのが楽そう。

xxTest.php
```php
$ds = new Iamapen\ExcelFriendlyDataSet\Database\DataSet\ExcelCsvDataSet();
```


## Operation

### Operation/MySqlBulkInsert
`PHPUnit_Extensions_Database_Operation_Insert` のバルクインサート版。  
MySQL専用。(一応SQLiteでも動く)

あまりに入力CSVが大きいと、`max_allowed_packet` の制限にかかる可能性がある。これは課題。

```php
use Iamapen\ExcelFriendlyDataSet\Database\DataSet\CommentableCsvDataSet;
use Iamapen\ExcelFriendlyDataSet\Database\Operation\MySqlBulkInsert;

// DataSet
$ds = new CommentableCsvDataSet('PATH/TO/CSV');

// 実行
$operation = new MySqlBulkInsert();
$operation->execute($this->getConnection(), $ds);
```


# 注意点・課題
- DataSet/ExcelCsvDataSet は現代では使うべきでないと思う。  
  UTF-8 で保存して、UTF-8 対応のエディタで編集するのがよい。例えば LibreOffice の Calc でできる。  
  Excelでやろうというのは、まともなエディタが存在しなかった時代の古いアプローチ。

- Operation/MySqlBulkInsert は、あまりに入力CSVが大きいと `max_allowed_packet` の制限にかかる可能性がある。

- PHP-5.3 サポートのために PHPUnit の 3.x と 4.x をベースにしている。  
  おそらく PHPUnit-5 系では動作しない。

- 正式なプロダクトでの運用実績がないため、品質は趣味レベル。
