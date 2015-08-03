<?php
$SqLite3Path = "./crawl/9sort.sqlite3";
$offset = 0;
$page   = 1;
$key    = "";

$query  = "select * from qiita_js  union
select * from hatena_js union
select * from qiita_php union
select * from hatena_php
order by date desc limit 7 offset ";

$nineSortDB = new SQLite3($SqLite3Path);
if($nineSortDB === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

//page番号がセットされているか、page番号の値が整数値であるか
if(isset($_GET["p"]) && ctype_digit($_GET["p"]) && 0 < $_GET["p"]) {
  $offset = htmlspecialchars(($_GET["p"]-1)*7);
  $page   = htmlspecialchars($_GET["p"]);
}

$backLink = "<a class='pure-button' href='./index.php?p=".($page-1)."'>Back</a>";
$nextLink = "<a class='pure-button nextbtn' href='./index.php?p=".($page+1)."'>Next</a>";

if(isset($_GET["q"]) && !empty($_GET["q"])) {
  $key = htmlspecialchars($_GET["q"]);
  $key = str_replace("　", " ", $key);
  $key = trim($key);
  $key = str_replace(" ", "%' and title like '%", $key);

  $query = "select * from qiita_js where title like '%{$key}%' union
  select * from qiita_php where title like '%{$key}%' union
  select * from hatena_js where title like '%{$key}%' union
  select * from hatena_php where title like '%{$key}%'
  order by date desc limit 7 offset ";

  $key = str_replace("%' and title like '%", " ", $key);

  $backLink = "<a class='pure-button pure-button-primary' href='./index.php?p=".($page-1)."&q=".$key."'>Back</a>";
  $nextLink = "<a class='pure-button pure-button-primary' href='./index.php?p=".($page+1)."&q=".$key."'>Next</a>";
}

$queryResults = $nineSortDB->query($query.$offset.";");
?>

<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>9sort Project</title>
  <link rel="stylesheet" href="//yui.yahooapis.com/pure/0.6.0/pure-min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="./css/common.css">
  <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
</head>
<body>
  <div class="header">
    <div class="home-menu pure-menu pure-menu-horizontal pure-menu-scrollable pure-menu-fixed">
      <a class="pure-menu-heading" href="./index.php">9sort v0.2</a>
      <form class="pure-menu-heading pure-form" method="get" action="index.php">
        <search class="search">
          <input type="text" class="pure-input-rounded" placeholder="検索" name="q" value="<?php echo $key; ?>">
          <button type="submit" class="search-btn fa fa-search"></button>
        </search>
      </form>
      <ul class="pure-menu-list">
        <li class="pure-menu-item"><a href="" class="pure-menu-link">About</a></li>
        <li class="pure-menu-item"><a href="" class="pure-menu-link">Log In</a></li>
      </ul>
    </div>
  </div>

  <div class="content-wrapper">

    <!-- <video autoplay loop class="fillWidth">
      <source src="img/Hello-World.mp4" type="video/mp4"/>Your browser does not support the video tag. I suggest you upgrade your browser.
      <source src="img/Hello-World.webm" type="video/webm"/>Your browser does not support the video tag. I suggest you upgrade your browser.
    </video> -->

    <div class="content">
      <h2 class="content-head">一覧</h2>
      <div class="pure-g">
        <?php
        $article = $queryResults->fetchArray(SQLITE3_ASSOC);
        $i = 0;
        while($article):
          $article['title'] = htmlspecialchars($article['title'], ENT_QUOTES|ENT_HTML5);
          $tags = htmlspecialchars("タグ: {$article['tag1']} {$article['tag2']} {$article['tag3']} {$article['tag4']}");
          $tags = str_replace(' 0', '', $tags);
        ?>
          <div class="article">
            <label for="Panel<?php echo $i;?>">
              <div class="l-box pure-u-1 pure-u-md-1 pure-u-lg-1">
                <h3 class="content-subhead">
                  <?php echo "<a href='{$article['url']}' target='_blank'><i class='fa fa-code'></i>{$article['title']}</a>"; ?>
                </h3>
                <?php
                echo "<p>{$tags}</p>";
                echo "<p>{$article["date"]}</p>"
                ?>
                <div class="is-right">
                  <i class="fa fa-play fa-rotate-90"></i>これを読むために参考になる記事
                </div>
              </div>
              <input type="checkbox" id="Panel<?php echo $i;?>" class="on-off" />
              <ul>
                <li><?php echo "<a href='{$article['url']}' target='_blank'><i class='fa fa-code'></i>{$article['title']}</a>"; ?></li>
                <li><?php echo "<a href='{$article['url']}' target='_blank'><i class='fa fa-code'></i>{$article['title']}</a>"; ?></li>
                <li><?php echo "<a href='{$article['url']}' target='_blank'><i class='fa fa-code'></i>{$article['title']}</a>"; ?></li>
              </ul>
            </label>
          </div>
        <?php
          $i++;
          $article = $queryResults->fetchArray(SQLITE3_ASSOC);
        endwhile;
        $queryResults->finalize();
        $nineSortDB->close();
        ?>
      </div>
      <div class="linkbtn is-center">
        <?php echo $backLink; ?>
        <?php echo $nextLink; ?>
      </div>
    </div>
    <div class="footer is-center">
      9sort Project. ver0.2 (> <)
    </div>
  </div>
</body>
</html>
