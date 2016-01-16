<?php
/** サンプルプログラム : nlp_ai/training_code/code-4-2-6-3.php
  * [説明]
  * 「4.2.6.けんてーごっこコーパスを扱うクラスの紹介」の内容を扱うサンプルプログラム。
  * Corpus_Kentei を使ったサンプルプログラムである。
  * 無作為にコーパスファイルを選択して、コーパスの内容を表示する。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_Kentei クラスを読み込む */
require_once( 'class/Corpus_Kentei.php' );

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei        = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    /* 訓練用コーパスのカテゴリ一覧 */
    $categoryList   = $ckentei->getTrainingCategoryList();
    
    /* 表示するカテゴリを無作為に選択 */
    shuffle( $categoryList );
    $showCategory   = $categoryList[0];
    
    /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
    $fileList       = $ckentei->getTrainingCorpusList( $showCategory );
    
    /* 表示するファイルを無作為に選択する */
    shuffle( $fileList );
    $selectedFile   = $fileList[0];
    
    printf( "*******************************************************\n" );
    printf( "けんてーごっこ コーパス :: 訓練用コーパスの表示\n" );
    printf( "表示カテゴリ = %s\n" , $showCategory );
    printf( "コーパスのパス = %s\n" , $selectedFile );
    printf( "*******************************************************\n" );
    
    $corpusBody     = $ckentei->getTrainingContent( $selectedFile );
    printf( "%s\n" , $corpusBody );
    printf( "\n\n" );
    
    /* 評価用コーパスのカテゴリ一覧 */
    $categoryList   = $ckentei->getTestCategoryList();
    
    /* 表示するカテゴリを無作為に選択 */
    shuffle( $categoryList );
    $showCategory   = $categoryList[0];
    
    /* 指定したカテゴリに含まれるコーパスファイルの一覧を取得する */
    $fileList       = $ckentei->getTestCorpusList( $showCategory );
    
    /* 表示するファイルを無作為に選択する */
    shuffle( $fileList );
    $selectedFile   = $fileList[0];
    
    printf( "*******************************************************\n" );
    printf( "けんてーごっこ コーパス :: 評価用コーパスのファイル一覧\n" );
    printf( "表示カテゴリ = %s\n" , $showCategory );
    printf( "コーパスのパス = %s\n" , $selectedFile );
    printf( "*******************************************************\n" );
    
    $corpusBody     = $ckentei->getTestContent( $selectedFile );
    printf( "%s\n" , $corpusBody );
    printf( "\n\n" );
    
}else{
    printf( "Corpus_Kentei オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}


?>