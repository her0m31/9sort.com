<?php
$request   = "https://www.kimonolabs.com/api/9kbvxj9s?kimmodify=1?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response  = file_get_contents($request);
$kimonoApi = json_decode($response, TRUE);
$empty     = 0;

$SqLite3Path = dirname(__FILE__)."/../../9sort.sqlite3";
$articleTable = new SQLite3($SqLite3Path);
if($articleTable === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

$countHatenaJs = count($kimonoApi);
for($i = 0; $i < $countHatenaJs; $i++) {
  $url = $articleTable->escapeString($kimonoApi[$i]['title']['href']);
  if(empty($kimonoApi[$i]['title']['href'])) {
    continue;
  }

  $title = $articleTable->escapeString($kimonoApi[$i]['title']['text']);
  $date  = $articleTable->escapeString($kimonoApi[$i]['date']);

  $tag1  = $articleTable->escapeString($kimonoApi[$i]['tag1']['text']);
  $tag2  = empty($kimonoApi[$i]['tag2']['text'])  ? $empty : $articleTable->escapeString($kimonoApi[$i]['tag2']['text']);
  $tag3  = empty($kimonoApi[$i]['tag3']['text'])  ? $empty : $articleTable->escapeString($kimonoApi[$i]['tag3']['text']);
  $tag4  = empty($kimonoApi[$i]['tag4']['text'])  ? $empty : $articleTable->escapeString($kimonoApi[$i]['tag4']['text']);

  $users = empty($kimonoApi[$i]['users']['text']) ? $empty : $articleTable->escapeString($kimonoApi[$i]['users']['text']);

  $articleTable->exec("INSERT OR IGNORE INTO hatena_js(title, url, date, tag1, tag2, tag3, tag4, stocks, score)
                        VALUES('$title', '$url', '$date', '$tag1', '$tag2', '$tag3', '$tag4', $users, $empty)");
}

print "Crawling hatena_js OK.\n";
$articleTable->close();
?>
