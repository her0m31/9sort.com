<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-2-4.php
  * [説明]
  * 「3.2.4.mecab をPHPから呼び出してみよう」の内容を扱うサンプルプログラム。
  * proc_open()関数でmecabプロセスに接続し、mecabから形態素解析処理結果を受け取る。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/** 
  * @var    array       双方向のやり取りを定義する配列
  */
$pipeDefine       = array();
$pipeDefine[0]    = array( 'pipe' , 'r' );
$pipeDefine[1]    = array( 'pipe' , 'w' );
$pipeDefine[2]    = array( 'file' , '/tmp/error_mecab.txt' , 'a' );

/** 
  * @var    string      mecabから見た現在ディレクトリ
  */
$cwd = '/tmp';


/** 
  * @var    array       双方向のやり取りをするためのポインタを格納する配列
  */
$pipes            = array();


/** 
  * @var    resource    プロセスハンドル
  */
$procHandle       = null;

$procHandle       = proc_open( '/usr/local/bin/mecab' , $pipeDefine, $pipes, $cwd, null );


if( is_resource( $procHandle ) ){
    while( true )
    {
        printf( '形態素解析する文章を入力して下さい:' );
        
        /** 
          * @var    string      形態素解析にかける文
          */
        $textInput  = null;
        
        /* 標準入力を利用し、キーボードからの入力を受け付ける */
        $stdin      = fopen( 'php://stdin' , 'r' );
        $textInput  = fgets( $stdin );
        fclose( $stdin );
        $textInput  = rtrim( $textInput );
        
        /* mecab に文を送る。改行コード CR＋LF を付与すること */
        fwrite( $pipes[0] , sprintf( "%s\r\n" , $textInput ) );
        
        /* mecab側の処理が終わるまで少し待つ */
        usleep( 1000 );
        
        printf( "\n\n" );
        printf( "[形態素に分解した結果]\n" );
        while( $eachResult = fgets( $pipes[1] ) )
        {
            if( substr( $eachResult , 0 , 3 ) !== 'EOS' ){
                list( $eachMorpheme , $eachInfo ) = explode( "\t" , $eachResult );
                printf( "%s\n" , $eachMorpheme );
            }else{
                break;
            }
        }
        printf( "\n\n" );
    }
}else{
    printf( "mecab との接続を開くことができません。処理を中断します。\n" );
    exit();
}


?>