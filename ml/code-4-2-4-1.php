<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-2-4-1.php
  * [説明]
  * 「4.2.4.コーパス「20 News Groups」を扱うクラスの紹介」の内容を扱うサンプルプログラム。
  * Corpus_20NewsGroups を使ったサンプルプログラムである。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_20NewsGroups クラスを読み込む */
require_once( 'class/Corpus_20NewsGroups.php' );

/** 
  * @var    object      Corpus_20NewsGroups クラスから生成したオブジェクト
  */
$c20ng      = new Corpus_20NewsGroups();

if( is_object( $c20ng ) ){
    /* 訓練用コーパスのカテゴリ一覧 */
    $categoryList   = $c20ng->getTrainingCategoryList();
    
    printf( "*******************************************************\n" );
    printf( "20 News Groups コーパス :: 訓練用コーパスのカテゴリ一覧\n" );
    printf( "*******************************************************\n" );
    foreach( $categoryList as $eachCategory )
    {
        printf( "%s\n" , $eachCategory );
    }
    
    printf( "\n\n" );
    
    /* 評価用コーパスのカテゴリ一覧 */
    $categoryList   = $c20ng->getTestCategoryList();
    
    printf( "*******************************************************\n" );
    printf( "20 News Groups コーパス :: 評価用コーパスのカテゴリ一覧\n" );
    printf( "*******************************************************\n" );
    foreach( $categoryList as $eachCategory )
    {
        printf( "%s\n" , $eachCategory );
    }
    
}else{
    printf( "Corpus_20NewsGroups オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>