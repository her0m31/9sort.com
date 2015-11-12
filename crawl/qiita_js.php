<?php
$request   = "https://www.kimonolabs.com/api/d5lcnqj6?kimmodify=1?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response  = file_get_contents($request);
$kimonoApi = json_decode($response, TRUE);
$empty     = 0;

$SqLite3Path  = "./9sort.sqlite3";
$articleTable = new SQLite3($SqLite3Path);
if($articleTable === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

$CountQiitaJs = count($kimonoApi);
for($i = 0; $i < $CountQiitaJs; $i++) {
  $url = $articleTable->escapeString($kimonoApi[$i]['title']['href']);
  if(empty($kimonoApi[$i]['title']['href'])) {
    continue;
  }

  $title   = $articleTable->escapeString($kimonoApi[$i]['title']['text']);

  $date    = $kimonoApi[$i]['date'];
  $date    = explode(' ', $date);
  $date[3] = convertMonth($date[3]);
  $date    = $date[5].'/'.$date[3].'/'.$date[4];
  $date    = str_replace(',', '', $date);
  $date    = $articleTable->escapeString($date);

  $tag1  = $articleTable->escapeString($kimonoApi[$i]['tag1']['text']);
  $tag2  = empty($kimonoApi[$i]['tag2']['text']) ? $empty : $articleTable->escapeString($kimonoApi[$i]['tag2']['text']);
  $tag3  = empty($kimonoApi[$i]['tag3']['text']) ? $empty : $articleTable->escapeString($kimonoApi[$i]['tag3']['text']);
  $tag4  = empty($kimonoApi[$i]['tag4']['text']) ? $empty : $articleTable->escapeString($kimonoApi[$i]['tag4']['text']);
  $stock = empty($kimonoApi[$i]['stock'])        ? $empty : $kimonoApi[$i]['stock'];
  $articleTable->exec("INSERT OR IGNORE INTO qiita_js(title, url, date, tag1, tag2, tag3, tag4, stocks, score)
                       VALUES('$title', '$url', '$date', '$tag1', '$tag2', '$tag3', '$tag4', $stock, $empty)");
}

print "Crawling qiita_js OK.\n";
$articleTable->close();
?>
