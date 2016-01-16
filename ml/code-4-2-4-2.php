<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-2-4-2.php
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
    
    /* 表示するカテゴリを無作為に選択 */
    shuffle( $categoryList );
    $showCategory   = $categoryList[0];
    
    printf( "*******************************************************\n" );
    printf( "20 News Groups コーパス :: 訓練用コーパスのファイル一覧\n" );
    printf( "表示カテゴリ = %s\n" , $showCategory );
    printf( "*******************************************************\n" );
    
    /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
    $fileList       = $c20ng->getTrainingCorpusList( $showCategory );
    
    /* 取得したファイルを30件表示する */
    $showCount      = 0;
    foreach( $fileList as $eachFile )
    {
        printf( "%s\n" , $eachFile );
        ++$showCount;
        if( $showCount > 30 ){
            break;
        }
    }
    printf( "\n\n" );
    
    /* 評価用コーパスのカテゴリ一覧 */
    $categoryList   = $c20ng->getTestCategoryList();
    
    /* 表示するカテゴリを無作為に選択 */
    shuffle( $categoryList );
    $showCategory   = $categoryList[0];
    
    printf( "*******************************************************\n" );
    printf( "20 News Groups コーパス :: 評価用コーパスのファイル一覧\n" );
    printf( "表示カテゴリ = %s\n" , $showCategory );
    printf( "*******************************************************\n" );
    
    /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
    $fileList       = $c20ng->getTestCorpusList( $showCategory );
    
    /* 取得したファイルを30件表示する */
    $showCount      = 0;
    foreach( $fileList as $eachFile )
    {
        printf( "%s\n" , $eachFile );
        ++$showCount;
        if( $showCount > 30 ){
            break;
        }
    }
    
}else{
    printf( "Corpus_20NewsGroups オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>