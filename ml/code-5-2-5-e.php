<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-2-5-e.php
  * [説明]
  * 「5.2.5.単語のスコアを確認しよう」の内容を扱うサンプルプログラム。
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
$bayes20ng->pdoPassword = '********';

/** 
  * @var    object      Corpus_20NewsGroups クラスから生成したオブジェクト
  */
$c20ng      = new Corpus_20NewsGroups();

if( is_object( $c20ng ) ){
    if( is_object( $bayes20ng ) ){
        printf( "***********************************************************\n" );
        printf( "ベイジアンフィルタ : 単語スコアの確認\n" );
        printf( "使用コーパス       : 20 News Groups コーパス\n" );
        printf( "***********************************************************\n" );
        
        /* 各カテゴリの分類辞書をメモリ上に読み込み、スコアが大きい順に100個ずつ表示する。 */
        $categoryList       = $c20ng->getTrainingCategoryList();
        
        foreach( $categoryList as $eachCategory )
        {
            printf( "●カテゴリ : %s\n" , $eachCategory );
            
            /** 分類辞書をメモリ上に読み込む。
              * より特徴的な単語を絞り込むため、そのカテゴリで20回以上出現している単語に絞り込む（第2引数に20を指定）
              */
            $bayes20ng->loadDictionary( $eachCategory , 20 );
            
            /* スコアが大きい順にソートする */
            arsort( $bayes20ng->dictionary );
            
            $showCount = 0;
            foreach( $bayes20ng->dictionary as $eachWord => $eachScore )
            {
                printf( "単語 %s = %f\n" , $eachWord , $eachScore );
                ++$showCount;
                if( $showCount >= 100 ){
                    break;
                }
            }
            printf( "\n\n" );
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