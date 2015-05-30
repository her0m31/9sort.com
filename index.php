<?php
$request = "https://www.kimonolabs.com/api/68jpwwa8?apikey=tuqSaJHUsKLOxKJBPr7vcxILzXfVDUIy";
$response = file_get_contents($request);
$results = json_decode($response, TRUE);

for($i = 0; $i < 800; $i++) {
  echo '<a href="';
  echo $results['results']['collection2'][$i]['Title']['href'];
  echo '">';
  echo htmlspecialchars($results['results']['collection2'][$i]['Title']['text'], ENT_QUOTES, 'UTF-8');
  echo "</a><br>";
}
