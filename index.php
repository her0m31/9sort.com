<?
$qiitaPhpPath        = './qiita-php.csv';
$qiitaPhpArticles    = [];
$qiitaPhpFilePointer = fopen($qiitaPhpPath, "r");

if($qiitaPhpFilePointer !== FALSE) {
  //CSVファイルから1行目を取得
  $qiitaPhpArticle = fgetcsv($qiitaPhpFilePointer, 0, ",");
  //CSVファイルの最終行まで取得
  while($qiitaPhpArticle !== FALSE) {
    $qiitaPhpArticles[] = $qiitaPhpArticle;
    $qiitaPhpArticle    = fgetcsv($qiitaPhpFilePointer, 0, ",");
  }

  fclose($qiitaPhpFilePointer);
}

$link = new SQLite3("9sort.sqlite3", SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, "08310831");
if (!$link) {
    die('接続失敗です。'.$sqliteerror);
}
print('接続に成功しました。<br>');
// SQLiteに対する処理
$link->close();
print('切断しました。<br>');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PURE</title>
  <link rel="stylesheet" href="./style.css">
</head>
<body>
  <?
  //for($i = 0; $i < 12; $i++) {
  //   echo "<img src='http://capture.heartrails.com/small/?";
  //   echo $results['results']['php'][$i]['title']['href'] . "'/>";
  //   echo $results['results']['php'][$i]['title']['text'];
  //   echo "<br>";
  // }
  ?>
</body>
</html>
