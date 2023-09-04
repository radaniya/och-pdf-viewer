<h1 align="center">
  <img alt="Logo" src=".github/logo.png" width="100px" />
</h1>

<h3 align="center">
  OCH PDF Viewer
</h3>

<div align="center">

[![made-by](https://img.shields.io/badge/made%20by-radaniya-orange)]()
[![license](https://img.shields.io/badge/license-MIT-green)](https://github.com/radaniya/och-pdf-viewer)

</div>

📋 Overview
-------------

OCH 電カル環境用の PDF Viewer


### フォルダ構成

```
/
├─ /config                設定ファイル群
|   ├─ users.json         ログインユーザーリスト
|   ├─ categories.json    文書カテゴリリスト
|   └─ pdfRootFolder.txt  PDF のあるフォルダを示す
├─ index.html           
├─ pdfviewer.php          本体
└─ getpdf.php             PDF ファイル読み込み用
```


📦 Requirements
------------

- PHP
- 開発環境としてVScode


📝 Settings
------------

`config/users.json` と `pdfRootFolder.txt` を適宜編集

`pdfRootFolder.txt` で示される PDF フォルダは下記のような構成にする

```
/pdf
├- PID_1
|  ├- PID_1-DATENUM-CATEGORY_NUM-SERIAL_NO_1.pdf
|  ├- PID_1-DATENUM-CATEGORY_NUM-SERIAL_NO_2.pdf
|  └- ...
├- PID_2
|  └- ...
└- ...
```


🪂 Deploy
--------------

全てのファイルをwebサーバーに配置

`index.html` に下記のようにアクセス

`http://PATH/TO/pdfviewer/index.html?user=USER_NAME&pass=PASSWORD&pid=FOLDERNAME`


📝 Memo
----

- FileMaker 15 server に付属の PHP バージョンは 5.6


📑 Refs
----


🧐 Author
------

* radaniya
* 所属： 医療法人社団クリノヴェイション
* E-mail： ry.adny at gmail.com


🧾 License
-------

*This Project* is under [MIT license](https://en.wikipedia.org/wiki/MIT_License).  
