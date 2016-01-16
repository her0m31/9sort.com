<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-2-6-1.php
  * [説明]
  * 「4.2.6.けんてーごっこコーパスを扱うクラスの紹介」の内容を扱うサンプルプログラム。
  * Corpus_Kentei を使ったサンプルプログラムである。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_Kentei クラスを読み込む */
require_once( 'class/Corpus_Kentei.php' );

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei    = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    /* 訓練用コーパスのカテゴリ一覧 */
    $categoryList   = $ckentei->getTrainingCategoryList();
    
    printf( "*******************************************************\n" );
    printf( "けんてーごっこ コーパス :: 訓練用コーパスのカテゴリ一覧\n" );
    printf( "*******************************************************\n" );
    foreach( $categoryList as $eachCategory )
    {
        printf( "%s\n" , $eachCategory );
    }
    
    printf( "\n\n" );
    
    /* 評価用コーパスのカテゴリ一覧 */
    $categoryList   = $ckentei->getTestCategoryList();
    
    printf( "*******************************************************\n" );
    printf( "けんてーごっこ コーパス :: 評価用コーパスのカテゴリ一覧\n" );
    printf( "*******************************************************\n" );
    foreach( $categoryList as $eachCategory )
    {
        printf( "%s\n" , $eachCategory );
    }
    
}else{
    printf( "Corpus_Kentei オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>