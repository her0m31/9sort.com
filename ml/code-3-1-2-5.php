<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-1-2-5.php
  * [説明]
  * 「3.1.2.5.辞書データを使って文章を分割してみよう」の内容を扱うサンプルプログラム。
  * 辞書データファイル(CSV形式)を読み込んで形態素辞書を構築し、形態素解析をする。
  * dictionary-3-1-2-4.csvが必要なので、サポートサイトからダウンロードして同じディレクトリに設置しておくこと。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/** 
  * @var    string      形態素解析する文
  */
$targetText     = '形態素辞書を使って日本語の文字列を形態素に分割します。';

/** 
  * @var    array       形態素辞書を定義する連想配列
  */
$dictionary               = array();

/** 
  * @var    string      形態素辞書ファイルのパス
  */
$dictionaryPath           = './dictionary-3-1-2-4.csv';


/* 形態素辞書ファイルから形態素の定義を読み込む */

/** 
  * @var    resource    形態素辞書ファイルへのファイルハンドル
  */
$fp     = fopen( $dictionaryPath , 'r' );
if( is_resource( $fp ) ){
    while( $eachLine = fgets( $fp ) )
    {
        $eachLine   = trim( $eachLine );
        $columns    = explode( ',' , $eachLine );
        
        /* 形態素は第1列である */
        $eachMorpheme   = $columns[0];
        
        /* 品詞の種別は第2列である */
        $eachPos        = $columns[1];
        
        /* 原形は第3列である */
        $eachPrototype  = $columns[2];
        
        /* 辞書用連想配列に登録する */
        $dictionary[$eachMorpheme]      = array( 'pos'  => $eachPos  , 'prototype' => $eachPrototype );
    }
    fclose( $fp );
}else{
    printf( "辞書ファイル %s を開くことができません。処理を中断します。\n" );
    exit();
}


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
foreach( $chunkResult as $eachNo => $eachWord )
{
    $eachPos        = null;
    $eachPrototype  = null;
    if( isset( $dictionary[$eachWord] ) ){
        $eachPos        = $dictionary[$eachWord]['pos'];
        $eachPrototype  = $dictionary[$eachWord]['prototype'];
    }
    printf( "( %02d 番目 ) %s  (品詞 = %s , 原形 = %s)\n" , $eachNo + 1 , $eachWord , $eachPos , $eachPrototype );
}

?>