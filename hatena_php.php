<?
$request   = "https://www.kimonolabs.com/api/9kbvxj9s?kimmodify=1?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response  = file_get_contents($request);
$kimonoApi = json_decode($response, TRUE);
$empty     = 0;

$SQLite3Path    = "./9sort.sqlite3";
$hatenaPhpTable = new SQLite3($SQLite3Path);
if($hatenaPhpTable === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

$CountHatenaPhp = count($kimonoApi);
for($i = 0; $i < $CountHatenaPhp; $i++) {
  //ユニークでNullを許さないURLで空でないかチェックする.空であればその後の処理をスキップ
  $url = $hatenaPhpTable->escapeString($kimonoApi[$i]['title']['href']);
  if(empty($kimonoApi[$i]['title']['href'])) {
    continue;
  }

  $title = $hatenaPhpTable->escapeString($kimonoApi[$i]['title']['text']);
  $date  = $hatenaPhpTable->escapeString($kimonoApi[$i]['date']);

  $tag1  = $hatenaPhpTable->escapeString($kimonoApi[$i]['tag1']['text']);
  //tag1と違いtag2~4,comments,stockに関しては空の可能性がある.タグやコメントが無い場合、エラーが発生するのでemptyで検査する.
  $tag2  = empty($kimonoApi[$i]['tag2']['text']) ? $empty : $hatenaPhpTable->escapeString($kimonoApi[$i]['tag2']['text']);
  $tag3  = empty($kimonoApi[$i]['tag3']['text']) ? $empty : $hatenaPhpTable->escapeString($kimonoApi[$i]['tag3']['text']);
  $tag4  = empty($kimonoApi[$i]['tag4']['text']) ? $empty : $hatenaPhpTable->escapeString($kimonoApi[$i]['tag4']['text']);

  //少数ストックと多数ストックがあるのでどちらかを入れる
  $users = empty($kimonoApi[$i]['users01']['text']) ? $kimonoApi[$i]['users02']['text'] : $kimonoApi[$i]['users01']['text'];
  //$users = "000 users";
  $users = explode(' ', $users);
  $users = $users[0];

  $hatenaPhpTable->exec("INSERT OR IGNORE INTO hatena_php(title, url, date, tag1, tag2, tag3, tag4, stocks, score)
                         VALUES('$title', '$url', '$date', '$tag1', '$tag2', '$tag3', '$tag4', $users, $empty)");
}

print("DB接続成功\n");
$hatenaPhpTable->close();
print("DB切断\n");
