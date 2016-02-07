<?php
    require_once("scrape.php");
    $category = isset($_GET["param"]) ? $_GET["param"] : "html";
    $testUrl = 'http://qiita.com/omega999/items/b9b75dc127053deea3a7';
?>
    <h1 align="center"><?php echo $category; ?>に関する記事</h1>
<?php for($i = 0; $i <= 3; $i++) : ?>
        <div align="center">
            <div class="article" >
                <label for="Panel<?php echo $i;?>">
                  <div class="l-box pure-u-1 pure-u-md-1 pure-u-lg-1">
                    <h3 class="content-subhead">
                      <?php echo "<a href='{$testUrl}' target='_blank'>推薦される記事タイトル".($i+1)."</a>"; ?>
                    </h3>
                    <?php
                    echo "<p>{$category}</p>";
                    //echo "<p>{$article["date"]}</p>"
                    ?>
                    <div class="is-right">
                      <i class="fa fa-play fa-rotate-90"></i>これを読むために参考になる記事
                    </div>
                  </div>
                  <input type="checkbox" id="Panel<?php echo $i;?>" class="on-off" />
                  <ul>
                    <li><?php echo "<a href='' target='_blank'>ここに</a>"; ?></li>
                    <li><?php echo "<a href='' target='_blank'>いろんな</a>"; ?></li>
                    <li><?php echo "<a href='' target='_blank'>記事くる</a>"; ?></li>
                  </ul>
                </label>
            </div>
        </div>
<?php endfor;
