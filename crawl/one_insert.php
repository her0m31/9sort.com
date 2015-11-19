<?php
$empty = 0;

if(count($argv) < 4) {
  print "one_insert.php -title -url -date.\n";
  print count($argv)."\n";
  return -1;
}

$SqLite3Path  = dirname(__FILE__)."/../../9sort.sqlite3";
$articleTable = new SQLite3($SqLite3Path);
if($articleTable === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

$title = $argv[1];
$users = $empty;
$date  = $argv[3];
$tag1  = "PHP";
$tag2  = "掲示板";
$tag3  = $empty;
$tag4  = $empty;
$url   = $argv[2];

$articleTable->exec("INSERT OR IGNORE INTO hatena_php(title, url, date, tag1, tag2, tag3, tag4, stocks, score)
                         VALUES('$title', '$url', '$date', '$tag1', '$tag2', '$tag3', '$tag4', $users, $empty)");

print "Insert Completed.\n";
$articleTable->close();
?>
