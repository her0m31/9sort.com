<?
$SqLite3Path  = "./9sort.sqlite3";
$articleTable = new SQLite3($SqLite3Path);
if($articleTable === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

require_once("./convert_month.php");
require_once("./qiita_php.php");
require_once("./qiita_js.php");
require_once("./hatena_php.php");
require_once("./hatena_js.php");

$articleTable->close();
