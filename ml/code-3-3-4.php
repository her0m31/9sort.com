<?php
/** サンプルプログラム : nlp_ai/training_code/code-3-3-4.php
  * [説明]
  * 「3.3.4.php_mecab を使ってみよう」の内容を扱うサンプルプログラム。
  * php_mecab を利用し、形態素解析処理を行う。
  * 
  * プログラムのエンコーディングはUTF-8。
  */


/** 
  * @var    object      php_mecab の Mecab_Tagger オブジェクト
  */
$mecab = new MeCab_Tagger();

if( is_object( $mecab ) ){
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
        
        $resultSet  = explode( "\n" , $mecab->parse( $textInput ) );
        printf( "\n\n" );
        printf( "[形態素に分解した結果]\n" );
        foreach( $resultSet as $eachResult )
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
    printf( "Mecab_Tagger オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>