<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-2-4.php
  * [説明]
  * 「5.2.4.「けんてーごっこコーパス」で機械学習処理をしよう」の内容を扱うサンプルプログラム。
  * ベイジアンフィルタの機械学習処理を実施する。
  * 
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_Kentei クラスを読み込む */
require_once( 'class/Corpus_Kentei.php' );

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/* BayesLearning_Kentei クラスを読み込む */
require_once( 'class/BayesLearning_Kentei.php' );

/** 
  * @var    object      BayesLearning_Kentei クラスから生成したオブジェクト
  */
$bayesKentei = new BayesLearning_Kentei();

/* MySQLに接続するためのパスワードを記入すること */
$bayesKentei->pdoPassword = '********';

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei    = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    if( is_object( $bayesKentei ) ){
        printf( "***********************************************************\n" );
        printf( "ベイジアンフィルタ : 機械学習処理\n" );
        printf( "使用コーパス       : けんてーごっこ コーパス\n" );
        printf( "***********************************************************\n" );
        
        /** (1) フィルタの登録処理
          * テーブル m_filter にフィルタを登録する。
          * フィルタ識別名を使い、フィルタが登録済みかどうか判定する。
          */
        $bayesKentei->registerFilters();
        
        /** 機械学習処理メイン：
          * 分類したいカテゴリのコーパス、分類したいカテゴリ以外のコーパスの順に
          * 機械学習処理を進めてゆく。
          */
        
        $categoryList       = $ckentei->getTrainingCategoryList();
        foreach( $categoryList as $eachCategory )
        {
            printf( "カテゴリ : %s を機械学習処理中\n" , $eachCategory );
            $bayesKentei->learn( $eachCategory );
        }
    }else{
        printf( "BayesLearning_20NewsGroups オブジェクトを作成できません。処理を中断します。\n" );
        exit();
    }
}else{
    printf( "Corpus_20NewsGroups オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}

?>