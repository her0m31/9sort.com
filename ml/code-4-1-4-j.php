<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-1-4-j.php
  * [説明]
  * 「4.1.4.形態素解析クラスを使ってみよう」の内容を扱うサンプルプログラム。
  * Morpheme クラスを使って日本語の形態素解析処理をする。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/** 
  * @var    object      Morpheme クラスから生成したオブジェクト
  */
$m      = new Morpheme();

if( is_object( $m ) ){
    while( true )
    {
        printf( '形態素解析にかける日本語の文章を入力してください( q:終了):' );
        
        /**
          * @var    string  入力された文章 
          */
        $inputText      = null;
        
        /* 標準入力を利用し、キーボードからの入力を受け付ける */
        $stdin          = fopen( 'php://stdin' , 'r' );
        $inputText      = fgets( $stdin );
        fclose( $stdin );
        $inputText      = rtrim( $inputText );
        
        if( strtolower( $inputText ) !== 'q' ){
            /* 形態素解析結果を取得する */
            $morphemeResult     = $m->parseJapanese( $inputText );
            
            printf( "【形態素解析結果】\n" );
            foreach( $morphemeResult as $eachResult )
            {
                printf( "[形態素] %s      ( 品詞種別 = %s , 原形 = %s , よみがな = %s )\n" ,
                        $eachResult['word'] ,
                        $eachResult['info']['type'] ,
                        $eachResult['info']['prototype'] ,
                        $eachResult['info']['yomigana'] );
            }
            printf( "\n\n" );
        }else{
            break;
        }
    }
}else{
    printf( "Morpheme オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>