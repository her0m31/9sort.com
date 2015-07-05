<?
$request = "https://www.kimonolabs.com/api/7lyezar2?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response = file_get_contents($request);
$results = json_decode($response, TRUE);

//タイトル,URL,日付,タグ１,タグ2,タグ3,タグ4,コメント数,ストック数
$fileName = "qiita-php.csv";
if(is_writable($fileName)) {
  $CountQiitaPhp = count($results['results']['php']);
  for($i = 0; $i < $CountQiitaPhp; $i++) {
    $title    = $results['results']['php'][$i]['title']['text'];
    $title    = str_replace(",", "、", $title);
    $title    = str_replace('"', '^', $title);
    $url      = $results['results']['php'][$i]['title']['href'];
    $date     = $results['results']['php'][$i]['date']['text'];
    $date     = explode(" ", $date);
    $date     = $date[3]."/".$date[4]."/".$date[5];
    $date     = str_replace(',', '', $date);

    $tag1     = $results['results']['php'][$i]['tag1']['text'];
    $tag2     = empty($results['results']['php'][$i]['tag2']['text'])     ? 0 : $results['results']['php'][$i]['tag2']['text'];
    $tag3     = empty($results['results']['php'][$i]['tag3']['text'])     ? 0 : $results['results']['php'][$i]['tag3']['text'];
    $tag4     = empty($results['results']['php'][$i]['tag4']['text'])     ? 0 : $results['results']['php'][$i]['tag4']['text'];
    $comments = empty($results['results']['php'][$i]['comments']['text']) ? 0 : $results['results']['php'][$i]['comments']['text'];
    $stock    = empty($results['results']['php'][$i]['stock'])            ? 0 : $results['results']['php'][$i]['stock'];

    $qiitaPhpRecord = $title.",".$url.",".$date.",".$tag1.",".$tag2.",".$tag3.",".$tag4.",".$comments.",".$stock."\n";
    file_put_contents($fileName, $qiitaPhpRecord, FILE_APPEND | LOCK_EX);
  }
}
