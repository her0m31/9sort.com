<?
require("./convert_month.php");

$request   = "https://www.kimonolabs.com/api/7lyezar2?kimmodify=1?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response  = file_get_contents($request);
$kimonoApi = json_decode($response, TRUE);
$empty     = 0;

$SQLite3Path   = "./9sort.sqlite3";
$qiitaPhpTable = new SQLite3($SQLite3Path);
if($qiitaPhpTable === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

$CountQiitaPhp = count($kimonoApi);
for($i = 0; $i < $CountQiitaPhp; $i++) {
  //ユニークでNullを許さないURLで空でないかチェックする.空であればその後の処理をスキップ
  $url = $qiitaPhpTable->escapeString($kimonoApi[$i]['title']['href']);
  if(empty($kimonoApi[$i]['title']['href'])) {
    continue;
  }

  $title   = $qiitaPhpTable->escapeString($kimonoApi[$i]['title']['text']);

  //qiitaは日付がuserが0000/00/00に投稿なので、日付部分のみ抜き出す。
  $date    = $kimonoApi[$i]['date']['text'];
  $date    = explode(' ', $date);
  $date[3] = convertMonth($date[3]);
  $date    = $date[5].'/'.$date[3].'/'.$date[4];
  $date    = str_replace(',', '', $date);
  $date    = $qiitaPhpTable->escapeString($date);

  $tag1  = $qiitaPhpTable->escapeString($kimonoApi[$i]['tag1']['text']);
  //tag1と違いtag2~4,comments,stockに関しては空の可能性がある.タグやコメントが無い場合、エラーが発生するのでemptyで検査する.
  $tag2  = empty($kimonoApi[$i]['tag2']['text']) ? $empty : $qiitaPhpTable->escapeString($kimonoApi[$i]['tag2']['text']);
  $tag3  = empty($kimonoApi[$i]['tag3']['text']) ? $empty : $qiitaPhpTable->escapeString($kimonoApi[$i]['tag3']['text']);
  $tag4  = empty($kimonoApi[$i]['tag4']['text']) ? $empty : $qiitaPhpTable->escapeString($kimonoApi[$i]['tag4']['text']);
  $stock = empty($kimonoApi[$i]['stock'])        ? $empty : $kimonoApi[$i]['stock'];
  //ID,タイトル,URL,日付,タグ１,タグ2,タグ3,タグ4,ストック数,スコア
  $qiitaPhpTable->exec("INSERT OR IGNORE INTO qiita_php(title, url, date, tag1, tag2, tag3, tag4, stocks, score)
                        VALUES('$title', '$url', '$date', '$tag1', '$tag2', '$tag3', '$tag4', $stock, $empty)");
}

print("DB接続成功\n");
$qiitaPhpTable->close();
print("DB切断\n");
