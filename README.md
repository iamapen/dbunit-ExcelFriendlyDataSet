ExcelCsvDataSet
===============

Excelで扱いやすい UTF-16(LE+BOM) のCSVを PHPUnit/dbunit で扱うための CsvDataSet。



Install
=======

依存ライブラリが OpenPear に存在するため、repositoriesの定義があわせて必要です。

composer.json

    repositories: [
      {
        "type": "pear",
        "url": "http://openpear.org"
      }
    ]

    require-dev: {
      "iamapen/excel-friendly-data-set": ">=0.0.1"
    }
