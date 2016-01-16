<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-1-1-3.php
  * [説明]
  * 「3.1.1.3.単語を正規化しよう」の内容を扱うサンプルプログラム。
  * 与えられた英文を英単語に分割し、各英単語に付着している不純物を除去したのち、単語構成文字を全て小文字に変換するという正規化処理を施す。
  * 
  * サンプルプログラム中で扱う英文サンプルは、Wikipedia から引用したものである。
  * 出展：Wikipedia ( http://en.wikipedia.org/wiki/Natural_language_processing )
  * プログラムのエンコーディングはUTF-8。
  */

/** 
  * @var    string      分割する英文サンプル
  */
$targetText     = 'Natural language processing (NLP) is a field of computer science, artificial intelligence, and linguistics concerned with the interactions between computers and human (natural) languages.';

/** 
  * @var    string      区切り文字。ここでは半角スペース。
  */
$separator      = ' ';

/** 
  * @var    array       分割結果
  */
$chunkResult    = null;

$chunkResult    = explode( $separator , $targetText );

printf( "[元の英文]\n" );
printf( "%s\n\n" , $targetText );
printf( "[分割で得られた単語数] %d\n" , sizeof( $chunkResult ) );

/* 分割で得られた単語を表示する */
foreach( $chunkResult as $eachPos => $eachWord )
{
    /* 単語の先頭に不純物が付着している場合は除去する */
    if( substr( $eachWord , 0 , 1 ) === '(' ){
        /* 単語の2文字目以降を取り出し、単語とする */
        $eachWord   = substr( $eachWord , 1 );
    }
    
    /* 単語の末尾に不純物が付着している場合は除去する */
    $eachTail   = substr( $eachWord , -1 , 1 );
    if( $eachTail === ')' || $eachTail === ',' || $eachTail === '.' ){
        /* 末尾の文字の手前までを取り出して、単語とする */
        $eachLength = strlen( $eachWord );
        $eachWord   = substr( $eachWord , 0 , $eachLength - 1 );
    }
    
    /* 単語を構成する文字を全て小文字に変換する */
    $eachWord       = strtolower( $eachWord );
    printf( "( %d 番目 ) %s\n" , $eachPos + 1 , $eachWord );
}

?>