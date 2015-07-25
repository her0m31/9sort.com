<?
$SqLite3Path = "./9sort.sqlite3";
$offset      = 0;
$page        = 1;

$nineSortDB = new SQLite3($SqLite3Path);
if($nineSortDB === FALSE) {
  die("DB接続失敗\n".$sqliteerror);
}

if(isset($_GET["page"]) == TRUE && ctype_digit($_GET["page"]) && 0 < $_GET["page"]) {
  $offset = ($_GET["page"] - 1) * 7;
  $page   = $_GET["page"];
}

$queryResults = $nineSortDB->query("select * from qiita_js union all select * from hatena_js
                               order by date desc limit 7 offset ". $offset .";");

$article = $queryResults->fetchArray(SQLITE3_ASSOC);
?>

<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>9sort.com Project</title>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/grids-responsive-min.css">
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
    <link rel="stylesheet" href="css/layouts/marketing.css">
  </head>

  <body>
    <div class="header">
      <div class="home-menu pure-menu pure-menu-horizontal pure-menu-scrollable pure-menu-fixed">
        <a class="pure-menu-heading" href="">9sort.com</a>
        <form class="pure-menu-heading pure-form"><input type="text" class="pure-input-rounded" placeholder="検索"></form>
        <ul class="pure-menu-list pure-menu-fixed">
          <li class="pure-menu-item"><a href="#" class="pure-menu-link">About</a></li>
          <li class="pure-menu-item"><a href="#" class="pure-menu-link">Sign Up</a></li>
          <li class="pure-menu-item"><a href="#" class="pure-menu-link">Log In</a></li>
        </ul>
      </div>
    </div>

    <div class="content-wrapper">
      <div class="content">
        <h2 class="content-head is-center">新着記事一覧</h2>
        <div class="pure-g">

          <?
            while($article == TRUE):
          ?>
            <div class="box">
              <div class="l-box-lrg is-center pure-u-1-6 pure-u-md-1-6 pure-u-lg-1-6">
                <?
                  echo '<img class="pure-img-responsive" src="http://capture.heartrails.com/small/?'.$article["url"].'">';
                ?>
              </div>
              <div class="l-box pure-u-5-6 pure-u-md-5-6 pure-u-lg-5-6">
                <h3 class="content-subhead">
                  <i class="fa fa-rocket"></i>
                  <?
                    echo "<a href='".$article["url"]."' target='_blank'>".$article["title"]."</a>";
                    echo "<d>".$article["date"]."</d>"
                  ?>
                </h3>
                  <?
                    echo "<p>タグ:".$article["tag1"].", ".$article["tag2"].", ".$article["tag3"].", ".$article["tag4"]."</p>"
                  ?>
              </div>
              <div class="bd1 bd">
                <div class="bdT"></div>
                <div class="bdB"></div>
                <div class="bdR"></div>
                <div class="bdL"></div>
              </div>
            </div>
          <?
            $article = $queryResults->fetchArray(SQLITE3_ASSOC);
            endwhile;

            $queryResults->finalize();
            $nineSortDB->close();
          ?>
        </div>
      </div>

      <div class="content-foot is-center">
        <?
          $page++;
          echo "<a href='./view.php?page=". $page ."'>Next...</a>";
        ?>
      </div>

      <div class="footer is-center">
        9sort.com Projects. ver.0.1 (> <)
      </div>

    </div>
  </body>
</html>
