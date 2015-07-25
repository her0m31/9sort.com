<?
$SqLite3Path = "./9sort.sqlite3";
$offset      = 0;

$nineSortDB = new SQLite3($SqLite3Path);
if($nineSortDB === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

if(isset($_GET["page"]) == TRUE && ctype_digit($_GET["page"]) && 0 < $_GET["page"]) {
  $offset = ($_GET["page"] - 1) * 7;
}

$results = $nineSortDB->query("select * from qiita_js union all select * from hatena_js
                               order by date desc limit 7 offset ". $offset .";");

$article = $results->fetchArray(SQLITE3_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title="9sort">
</head>
<body>
  <?
  $row = $results->fetchArray(SQLITE3_ASSOC);
  while($row == TRUE) {
    echo "<div>";
    echo "<img src='http://capture.heartrails.com/small/?";
    echo $row['url'] . "'/>";

    echo "<a href='".$row["url"]."' target='_blank'>".$row["title"]."</a>";
    echo $row["date"].$row["tag1"].$row["tag2"].$row["tag3"].$row["tag4"].$row["stocks"];
    echo "</div>";

    $row = $results->fetchArray(SQLITE3_ASSOC);
  }

  $results->finalize();
  $nineSortDB->close();
  ?>
  </body>
</html>
