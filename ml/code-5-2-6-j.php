<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-2-6-j.php
  * [説明]
  * 「5.2.6.単語のスコアを確認しよう：けんてーごっこコーパス編」の内容を扱うサンプルプログラム。
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
$bayesKentei  = new BayesLearning_Kentei();

/* MySQLに接続するためのパスワードを記入すること */
$bayesKentei->pdoPassword = '********';

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei     = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    if( is_object( $bayesKentei ) ){
        printf( "***********************************************************\n" );
        printf( "ベイジアンフィルタ : 単語スコアの確認\n" );
        printf( "使用コーパス       : けんてーごっこ コーパス\n" );
        printf( "***********************************************************\n" );
        
        /* 各カテゴリの分類辞書をメモリ上に読み込み、スコアが大きい順に100個ずつ表示する。 */
        $categoryList       = $ckentei->getTrainingCategoryList();
        
        foreach( $categoryList as $eachCategory )
        {
            printf( "●カテゴリ : %s\n" , $eachCategory );
            
            /** 分類辞書をメモリ上に読み込む。
              * より特徴的な単語を絞り込むため、そのカテゴリで20回以上出現している単語に絞り込む（第2引数に20を指定）
              */
            $bayesKentei->loadDictionary( $eachCategory , 20 );
            
            /* スコアが大きい順にソートする */
            arsort( $bayesKentei->dictionary );
            
            $showCount = 0;
            foreach( $bayesKentei->dictionary as $eachWord => $eachScore )
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
        printf( "BayesLearning_Kentei オブジェクトを作成できません。処理を中断します。\n" );
        exit();
    }
}else{
    printf( "Corpus_Kentei オブジェクトを作成できません。処理を中断します。\n" );
    exit();
}

?>