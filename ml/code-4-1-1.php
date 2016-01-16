<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-1-1.php
  * [説明]
  * 「4.1.1.オブジェクト指向の基本」の内容を扱うサンプルプログラム。
  * クラスとオブジェクトのふるまいを確かめる実験をする。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* クラスの名前は Human とする */
class Human
{
    /* プロパティは var 文で定義する */
    
    /* 住んでいる地域 */
    var $habitat    = '東京都';
    
    /* 年齢 */
    var $age        = 20;
    
    /* 職業 */
    var $job        = '大学生';
    
    /* 持ち物：配列型のプロパティとして定義する */
    var $item       = array( 'ノートPC' , 'モバイルルーター' , 'リュックサック' , '財布' );
    
    /* 体力 */
    var $hp         = 100;
    
    /* 集中力 */
    var $mp         = 50;
    
    /** メソッド：歩く
      * 体力を消費する。気分転換になるので、集中力が回復する。
      */
    function walk()
    {
        if( $this->hp > 10 ){
            printf( "30分ほど歩いた。血の巡りが良くなったようだ。\n" );
            printf( "気分転換になった。\n" );
            $this->hp     -= 10;
            $this->mp     += 5;
        }else{
            printf( "へとへとで歩けない。\n" );
        }
    }
    
    
    /** メソッド：勉強する
      * 集中力を消費する。
      */
    function study()
    {
        if( $this->mp > 5 ){
            printf( "本を開いて勉強をした。充実感が得られた。\n" );
            $this->mp   -= 5;
        }else{
            printf( "頭が痛い。目がチカチカする。勉強にうちこめそうにない。\n" );
        }
    }
    
    
    /** メソッド：仮眠する
      * 体力と集中力を回復する。
      */
    function nap()
    {
        $this->hp       = 100;
        $this->mp       = 50;
        printf( "仮眠を取った。心身の疲れが取れたようだ。\n" );
    }
    
    
    /** メソッド：持ち物一覧を表示する。
      * 
      */
    function showInventory()
    {
        printf( "●持ち物一覧\n" );
        foreach( $this->item as $eachItem )
        {
            printf( "・%s\n" , $eachItem );
        }
    }
    
    
    /** メソッド：ステータスを表示する。
      * 
      */
    function showStatus()
    {
        printf( "\n" );
        printf( "●ステータス\n" );
        printf( "HP         %d\n" , $this->hp );
        printf( "MP         %d\n" , $this->mp );
        printf( "年齢       %d\n" , $this->age );
        printf( "職業       %s\n" , $this->job );
        printf( "\n\n" );
    }
}


/** 
  * @var    object      Human クラスから生成した Humanオブジェクト
  */
$sampleHuman = new Human();

if( is_object( $sampleHuman ) ){
    while( true )
    {
        $sampleHuman->showStatus();
        
        printf( 'コマンドを入力して下さい( w:歩く s:勉強する n:仮眠する i:持ち物一覧 q:終了):' );
        
        /** 
          * @var    string      形態素解析にかける文
          */
        $textInput  = null;
        
        /* 標準入力を利用し、キーボードからの入力を受け付ける */
        $stdin      = fopen( 'php://stdin' , 'r' );
        $textInput  = fgets( $stdin );
        fclose( $stdin );
        $textInput  = rtrim( $textInput );
        
        switch( strtolower( $textInput ) )
        {
            case 'w' :
                $sampleHuman->walk();
                break;
            
            case 's' :
                $sampleHuman->study();
                break;
            
            case 'n' :
                $sampleHuman->nap();
                break;
                
            case 'i' :
                $sampleHuman->showInventory();
                break;
                
            case 'q' :
                break 2;
        }
    }
}else{
    printf( "Human オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>