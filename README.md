ExcelFriendlyDataSet
===============

Excelで扱いやすい UTF-16(LE+BOM) のCSVを phpunit/dbunit で使うための CsvDataSet。

Excelでは UTF-8 のcsvをまともに編集(とくに保存)ができないが、
UTF-16(LE+BOM) にすれば「D&Dで開く」「Ctrl+S」で保存など比較的自然に編集でき、
テストデータにUnicode文字も使える。

代わりにテストコード内で UTF-16 -> UTF-8 変換が必要になるので、これを行う。


なおExcelからは"Unicode", sakuraエディタでは"Unicode", vimでは"utf16le", mbstringでは"UTF-16"で扱える。
新規CSV作成時はテキストエディタでUTF-16のファイルを作成してそれをExcelにD&Dするのが楽そう。


Install
=======


composer.json

    require-dev: {
      "iamapen/excel-friendly-data-set": ">=1.0.0"
    }

Usage
=====

## 1. Excelで扱いやすい UTF-16(LE+BOM) のCSVを UTF-8 に変換しながら取り込む。

ExcelCsvDataSet を使う。

xxTest.php

    $ds = new Iamapen\ExcelFriendlyDataSet\ExcelCsvDataSet();

## 2. CSVの左n列を無視する。その列はテストデータに対してのコメントに使える。

    $ds = new Iamapen\ExcelFriendlyDataSet\ExcelCsvDataSet(",");
    $ds->setIgnoreColumnCount(1);    // 1列目を無視

sample.csv

    最初はコメント,id,user_name
    男性,1,taro soramame
    女性,2,arare norimaki
    1ヶ月以上ログインしてないユーザ,3,akane kimidori

## 3. UTF-16(LE+BOM)は使わず、コメント機能だけが必要

CommentableCsvDataSet を使う。

    $ds = new Iamapen\ExcelFriendlyDataSet\CommentableCsvDataSet();
    $ds->setIgnoreColumnCount(2);    // 左から2列を無視

