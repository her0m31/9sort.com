<?
$request = "https://www.kimonolabs.com/api/7lyezar2?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response = file_get_contents($request);
$results = json_decode($response, TRUE);
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PURE</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="./style.css">
  </head>
  <body>
      <?
        for($i = 0; $i < 12; $i++) {
          echo "<img src='http://capture.heartrails.com/small/?";
          echo $results['results']['php'][$i]['title']['href'] . "'/>";
          echo $results['results']['php'][$i]['title']['text'];
          echo "<br>";
        }
      ?>
  </body>
</html>
