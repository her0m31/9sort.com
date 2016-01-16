<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-1-1-1.php
  * [説明]
  * 「3.1.1.1.文字列を分割しよう」の内容を扱うサンプルプログラム。
  * 与えられた英文を英単語に分割して表示する。
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
    printf( "( %d 番目 ) %s\n" , $eachPos + 1 , $eachWord );
}

?>