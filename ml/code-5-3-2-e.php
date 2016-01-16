<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-3-2-e.php
  * [説明]
  * 「5.3.2.文書の判定用スコアを計算しよう：20 News Groupsコーパス編」の内容を扱うサンプルプログラム。
  * 文書の判定用スコアを計算する。
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
        printf( "ベイジアンフィルタ : 文書の判定用スコアの計算\n" );
        printf( "使用コーパス       : 20 News Groups コーパス\n" );
        printf( "***********************************************************\n" );
        
        /* 各カテゴリの分類辞書をメモリ上に読み込み、無作為に選択したコーパスの判定用スコアを計算する。 */
        $categoryList       = $c20ng->getTrainingCategoryList();
        
        foreach( $categoryList as $eachCategory )
        {
            printf( "●カテゴリ : %s\n" , $eachCategory );
            
            /** 分類辞書をメモリ上に読み込む。
              * 判定用スコアを計算するので、単語の最低出現回数は2回に引き下げる（第2引数に2を指定）
              */
            $bayes20ng->loadDictionary( $eachCategory , 2 );
            
            /* カテゴリに含まれるコーパスファイルの一覧を取得する */
            $fileList       = $c20ng->getTrainingCorpusList( $eachCategory );
            
            shuffle( $fileList );
            
            /* 各カテゴリのコーパス10個を無作為に選択し、判定用スコアを計算し表示する */
            foreach( $fileList as $eachPos => $eachFile )
            {
                if( $eachPos < 10 ){
                    $corpusBody             = $c20ng->getTrainingContent( $eachFile );
                    $documentScore          = $bayes20ng->culcDocumentScore( $corpusBody );
                    printf( "コーパス %s : 判定用スコア = %f\n" , $eachFile , $documentScore );
                }else{
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