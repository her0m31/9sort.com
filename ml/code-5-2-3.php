<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-2-3.php
  * [説明]
  * 「5.2.3.「20 News Groups」コーパスから単語のスコアを求めよう」の内容を扱うサンプルプログラム。
  * ベイジアンフィルタの機械学習処理を実施する。
  *
  * プログラムのエンコーディングはUTF-8。
  */

/* Corpus_20NewsGroups クラスを読み込む */
require_once( 'class/Corpus_20NewsGroups.php' );

/* Morpheme クラスを読み込む */
require_once( 'class/Morpheme.php' );

/* BayesLearning_20NewsGroups クラスを読み込む */
require_once( 'class/BayesLearning_20NewsGroups.php' );

/**
  * @var    object      BayesLearning_20NewsGroups クラスから生成したオブジェクト
  */
$bayes20ng  = new BayesLearning_20NewsGroups();

/* MySQLに接続するためのパスワードを記入すること */
$bayes20ng->pdoPassword = 'root';

/**
  * @var    object      Corpus_20NewsGroups クラスから生成したオブジェクト
  */
$c20ng      = new Corpus_20NewsGroups();

if( is_object( $c20ng ) ){
    if( is_object( $bayes20ng ) ){
        printf( "***********************************************************\n" );
        printf( "ベイジアンフィルタ : 機械学習処理\n" );
        printf( "使用コーパス       : 20 News Groups コーパス\n" );
        printf( "***********************************************************\n" );

        /** (1) フィルタの登録処理
          * テーブル m_filter にフィルタを登録する。
          * フィルタ識別名を使い、フィルタが登録済みかどうか判定する。
          */
        $bayes20ng->registerFilters();

        /** 機械学習処理メイン：
          * 分類したいカテゴリのコーパス、分類したいカテゴリ以外のコーパスの順に
          * 機械学習処理を進めてゆく。
          */

        $categoryList       = $c20ng->getTrainingCategoryList();
        foreach( $categoryList as $eachCategory )
        {
            printf( "カテゴリ : %s を機械学習処理中\n" , $eachCategory );
            $bayes20ng->learn( $eachCategory );
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
