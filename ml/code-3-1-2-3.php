<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-1-2-3.php
  * [説明]
  * 「3.1.2.3.区切り文字だけでは対応できない場合を考えよう」の内容を扱うサンプルプログラム。
  * 日本語の文から、形態素辞書を使って形態素を抽出する。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/** 
  * @var    string      形態素解析する文
  */
$targetText     = '簡易型日本語形態素抽出処理システム';

/** 
  * @var    array       形態素辞書を定義する連想配列
  */
$dictionary               = array();
$dictionary['簡易']       = true;
$dictionary['型']         = true;
$dictionary['日本語']     = true;
$dictionary['形態素']     = true;
$dictionary['抽出']       = true;
$dictionary['処理']       = true;
$dictionary['システム']   = true;


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
    /* 現在位置 $cursor から 1 ～ 4文字を抽出し、形態素かどうか判定する */
    
    /** 
      * @var    boolean     形態素が見つかったか？
      */
    $flagMorphemeFound          = false;
    
    /** 
      * @var    int         見つかった形態素の最大長
      */
    $maxMorphemeLengthFound     = 0;
    
    /** 
      * @var    string      見つかった形態素の中で最長のもの
      */
    $morphemeFoundLongest      = null;
    
    for( $extractLength = 1 ; $extractLength <= 4 ; $extractLength++ )
    {
        /** 
          * @var    string  位置 $cusor から $extractLength 文字を取り出したもの
          */
        $eachChar    = mb_substr( $targetText , $cursor , $extractLength , 'utf-8' );
        if( isset( $dictionary[$eachChar] ) ){
            /* 形態素が見つかった印をつける */
            $flagMorphemeFound              = true;
            
            /* 形態素の中で最長のものを記録する。 */
            if( $extractLength > $maxMorphemeLengthFound ){
                $maxMorphemeLengthFound     = $extractLength;
                $morphemeFoundLongest       = $eachChar;
            }
        }
    }
    
    /* 形態素が見つかった場合は、$chunkResult に追加する */
    if( $flagMorphemeFound === true ){
        if( strlen( $buffer ) > 0 ){
            $chunkResult[]      = $buffer;
            $buffer             = '';
        }
        $chunkResult[]          = $morphemeFoundLongest;
        $cursor                += $maxMorphemeLengthFound;
    }else{
        /* 形態素が見つからない場合は、現在位置の1文字をバッファに追加する */
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