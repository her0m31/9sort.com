<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-1-1-5.php
  * [説明]
  * 「3.1.1.5.英語固有の実装：省略語を識別を速くする」の内容を扱うサンプルプログラム。
  * 連想配列を使って省略語の判定処理を速くする。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/** 
  * @var    string      分割する英文サンプル
  */
$targetText     = 'I\'m going to meet Mr. Smith at 3 P.M.';

/** 
  * @var    string      区切り文字。ここでは半角スペース。
  */
$separator      = ' ';

/** 
  * @var    array       分割結果
  */
$chunkResult    = null;


/** 
  * @var    array       省略語のリストを格納した連想配列。
  *                     クリーニング＆正規化の後に使うので、すべて小文字で表記してある。
  */
$abbreviationList           = array();
$abbreviationList['mr']     = true;
$abbreviationList['mrs']    = true;
$abbreviationList['p.m']    = true;
$abbreviationList['a.m']    = true;
$abbreviationList['u.s']    = true;

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
    
    /* 省略語だった場合、末尾のピリオドを復活させる */
    if( isset( $abbreviationList[$eachWord] ) === true ){
        $eachWord .= '.';
    }
    
    printf( "( %d 番目 ) %s\n" , $eachPos + 1 , $eachWord );
}

?>