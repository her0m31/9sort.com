<?php
function text_scrape($url) {
    $dom = new DOMDocument;
    @$dom->loadHTMLFile($url);
    $xpath = new DOMXPath($dom);
    foreach ($xpath->query("//section[@itemprop='articleBody']") as $node) {
        //MD記法のテキストも含まれているので削除
        $removeDom = $xpath->query("//div[@class='hidden']", $node);
        $node->removeChild($removeDom->item(0));
        $text = $node->nodeValue;
    }
    return $text;
}
//echo text_scrape("http://qiita.com/omega999/items/b9b75dc127053deea3a7");
