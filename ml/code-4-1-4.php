<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-1-4.php
  * [説明]
  * 「4.1.4.形態素解析クラスを使ってみよう」の内容を扱うサンプルプログラム。
  * Morpheme クラスを使って形態素解析処理をする。
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
        $sampleHuman->showStatus();
        
        printf( '処理を選んで下さい( j:日本語を形態素解析する e:英語を形態素解析する q:終了):' );
        
        /**
          * @var    string  入力された処理の種類 
          */
        $inputCommand   = null;
        
        /* 標準入力を利用し、キーボードからの入力を受け付ける */
        $stdin          = fopen( 'php://stdin' , 'r' );
        $inputCommand   = fgets( $stdin );
        fclose( $stdin );
        $inputCommand   = rtrim( $inputCommand );
        
        switch( strtolower( $inputCommand ) )
        {
            case 'w' :
                printf( "日本語のテキストを入力して下さい:" );
                
                $sampleHuman->walk();
                break;
            
            case 's' :
                $sampleHuman->study();
                break;
            
            case 'q' :
                break 2;
        }
    }
}else{
    printf( "Morpheme オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>