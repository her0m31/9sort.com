<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-1-2-2.php
  * [説明]
  * 「3.1.2.2.区切り文字列を使って文章を分割してみよう」の内容を扱うサンプルプログラム。
  * 日本語の文から、区切り文字列を境目として形態素を抽出する。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/** 
  * @var    string      形態素解析する文
  */
$targetText     = '日本語の文章を形態素に分解しましょう';

/** 
  * @var    array       区切り文字列を定義する連想配列
  */
$separatorList          = array();
$separatorList['て']    = true;
$separatorList['に']    = true;
$separatorList['を']    = true;
$separatorList['は']    = true;
$separatorList['の']    = true;
$separatorList['が']    = true;
$separatorList['で']    = true;
$separatorList['と']    = true;
$separatorList['へ']    = true;
$separatorList['も']    = true;
$separatorList['や']    = true;
$separatorList['よ']    = true;
$separatorList['ね']    = true;
$separatorList['から']  = true;
$separatorList['より']  = true;
$separatorList['まで']  = true;
$separatorList['ので']  = true;
$separatorList['ため']  = true;
$separatorList['やら']  = true;
$separatorList['なり']  = true;
$separatorList['だの']  = true;
$separatorList['だけ']  = true;
$separatorList['ほど']  = true;
$separatorList['など']  = true;
$separatorList['こそ']  = true;
$separatorList['しか']  = true;
$separatorList['でも']  = true;
$separatorList['さえ']  = true;
$separatorList['ても']  = true;
$separatorList['たり']  = true;
$separatorList['ばかり']    = true;
$separatorList['くらい']    = true;
$separatorList['ながら']    = true;
$separatorList['けれども']  = true;
$separatorList['ところが']  = true;
$separatorList['、']      = true;
$separatorList['。']      = true;
$separatorList['（']      = true;
$separatorList['）']      = true;
$separatorList['＜']      = true;
$separatorList['＞']      = true;
$separatorList['「']      = true;
$separatorList['」']      = true;
$separatorList['！']      = true;
$separatorList['？']      = true;
$separatorList['，']      = true;
$separatorList['．']      = true;
$separatorList['・']      = true;


/** 
  * @var    array       抽出結果
  */
$chunkResult    = array();

/** 
  * @var    int     解析する文章の文字数
  */
$maxLength      = mb_strlen( $targetText , 'utf-8' );


/** 
  * @var    string      バッファ変数
  */
$buffer         = '';


/** 
  * @var    int     文字列解析の現在位置
  */
$cursor         = 0;

while( $cursor < $maxLength )
{
    /* 現在位置 $cursor から 1 ～ 3文字を抽出し、区切り文字列かどうか判定する */
    
    /** 
      * @var    boolean     区切り文字列が見つかったか？
      */
    $flagSeparatorFound     = false;
    
    /** 
      * @var    int         見つかった区切り文字列の最大長
      */
    $maxSeparatorLengthFound    = 0;
    
    /** 
      * @var    string      見つかった区切り文字列の中で最長のもの
      */
    $separatorFoundLongest      = null;
    
    for( $extractLength = 1 ; $extractLength <= 3 ; $extractLength++ )
    {
        /** 
          * @var    string  位置 $cusor から $extractLength 文字を取り出したもの
          */
        $eachChar    = mb_substr( $targetText , $cursor , $extractLength , 'utf-8' );
        if( isset( $separatorList[$eachChar] ) ){
            /* 区切り文字列が見つかった印をつける */
            $flagSeparatorFound     = true;
            
            /* 区切り文字列の中で最長のものを記録する。 */
            if( $extractLength > $maxSeparatorLengthFound ){
                $maxSeparatorLengthFound    = $extractLength;
                $separatorFoundLongest      = $eachChar;
            }
        }
    }
    
    /* 区切り文字列が見つかった場合は、形態素として取り出す */
    if( $flagSeparatorFound === true ){
        if( strlen( $buffer ) > 0 ){
            $chunkResult[]      = $buffer;
            $buffer             = '';
        }
        $chunkResult[]          = $separatorFoundLongest;
        $cursor                += $maxSeparatorLengthFound;
    }else{
        /* 区切り文字列が見つからない場合は、現在位置の1文字をバッファに追加する */
        $buffer                .= mb_substr( $targetText , $cursor , 1 , 'utf-8' );
        ++$cursor;
    }
}

/* もしバッファ変数の中身があったら、形態素として追加しておく */
if( strlen( $buffer ) > 0 ){
    $chunkResult[]      = $buffer;
}


printf( "[元の文]\n" );
printf( "%s\n\n" , $targetText );
printf( "[分割で得られた単語数] %d\n" , sizeof( $chunkResult ) );

/* 分割で得られた単語を表示する */
foreach( $chunkResult as $eachPos => $eachWord )
{
    printf( "( %d 番目 ) %s\n" , $eachPos + 1 , $eachWord );
}

?>