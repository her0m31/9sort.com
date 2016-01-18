<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-3-3-j.php
  * [説明]
  * 「5.3.3.文書の判定用スコアを計算しよう：けんてーごっこコーパス編」の内容を扱うサンプルプログラム。
  * 文書の判定用スコアを計算する。
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
$ckentei      = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    if( is_object( $bayesKentei ) ){
        printf( "***********************************************************\n" );
        printf( "ベイジアンフィルタ : 文書の判定用スコアの計算\n" );
        printf( "使用コーパス       : けんてーごっこ コーパス\n" );
        printf( "***********************************************************\n" );
        
        /* 各カテゴリの分類辞書をメモリ上に読み込み、無作為に選択したコーパスの判定用スコアを計算する。 */
        $categoryList       = $ckentei->getTrainingCategoryList();
        
        foreach( $categoryList as $eachCategory )
        {
            printf( "●カテゴリ : %s\n" , $eachCategory );
            
            /** 分類辞書をメモリ上に読み込む。
              * 判定用スコアを計算するので、単語の最低出現回数は2回に引き下げる（第2引数に2を指定）
              */
            $bayesKentei->loadDictionary( $eachCategory , 2 );
            
            /* カテゴリに含まれるコーパスファイルの一覧を取得する */
            $fileList       = $ckentei->getTrainingCorpusList( $eachCategory );
            
            shuffle( $fileList );
            
            /* 各カテゴリのコーパス10個を無作為に選択し、判定用スコアを計算し表示する */
            foreach( $fileList as $eachPos => $eachFile )
            {
                if( $eachPos < 10 ){
                    $corpusBody             = $ckentei->getTrainingContent( $eachFile );
                    $documentScore          = $bayesKentei->culcDocumentScore( $corpusBody );
                    printf( "コーパス %s : 判定用スコア = %f\n" , $eachFile , $documentScore );
                }else{
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