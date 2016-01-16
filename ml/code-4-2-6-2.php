<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-2-6-2.php
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
$ckentei     = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    /* 訓練用コーパスのカテゴリ一覧 */
    $categoryList   = $ckentei->getTrainingCategoryList();
    
    /* 表示するカテゴリを無作為に選択 */
    shuffle( $categoryList );
    $showCategory   = $categoryList[0];
    
    printf( "*******************************************************\n" );
    printf( "けんてーごっこ コーパス :: 訓練用コーパスのファイル一覧\n" );
    printf( "表示カテゴリ = %s\n" , $showCategory );
    printf( "*******************************************************\n" );
    
    /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
    $fileList       = $ckentei->getTrainingCorpusList( $showCategory );
    
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
    $categoryList   = $ckentei->getTestCategoryList();
    
    /* 表示するカテゴリを無作為に選択 */
    shuffle( $categoryList );
    $showCategory   = $categoryList[0];
    
    printf( "*******************************************************\n" );
    printf( "けんてーごっこ コーパス :: 評価用コーパスのファイル一覧\n" );
    printf( "表示カテゴリ = %s\n" , $showCategory );
    printf( "*******************************************************\n" );
    
    /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
    $fileList       = $ckentei->getTestCorpusList( $showCategory );
    
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