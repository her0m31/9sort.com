<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>9sort Project</title>
  <link rel="stylesheet" href="//yui.yahooapis.com/pure/0.6.0/pure-min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/common.css">
  <link rel="stylesheet" href="css/frame.css">
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
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
        <li class="pure-menu-item"><a href="index.php" class="pure-menu-link fa fa-play"> Top</a></li>
        <li class="pure-menu-item"><a href="" class="pure-menu-link">About</a></li>
        <li class="pure-menu-item"><a href="" class="pure-menu-link">Log In</a></li>
      </ul>
    </div>
  </div>
  <div class="content-wrapper">
    <div class="left">
      カテゴリ
      <ul>
        <li class="category">php</li>
        <li class="category">ruby</li>
        <li class="category">JavaScript</li>
      </ul>
    </div>
    <div class="right">
      選択対象カテゴリ記事一覧
    </div>
  </div>
  <script>
    $('.category').click(function(){
      var url = "get_news.php?param=" + $(this).text();
      $.ajax({
          type: "GET",
          data: {
              "param": $(this).text()
          },
          url: "get_news.php",
          dataType: "text",
          cache: false,
          success: function(data, textStatus){
              console.log(data);
              console.log(url);
              $(".right").html(data);
          },
          error: function(xhr, textStatus, errorThrown){
              // エラー処理
              alert("ng!");
          }
      });
    });
  </script>
</body>
</html>
