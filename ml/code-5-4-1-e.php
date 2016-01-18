<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-4-1-e.php
  * [説明]
  * 「5.4.1.文書の判定用スコアの分布を調べよう」の内容を扱うサンプルプログラム。
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
        printf( "ベイジアンフィルタ : 文書の判定用スコアの分布の調査\n" );
        printf( "使用コーパス       : 20 News Groups コーパス\n" );
        printf( "***********************************************************\n" );
        
        /* 各カテゴリの分類辞書をメモリ上に読み込み、判定用スコアの分布を計算する。 */
        $categoryList       = $c20ng->getTrainingCategoryList();
        
        foreach( $categoryList as $eachCategory )
        {
            printf( "------------------------------------------------------------\n" );
            printf( "●カテゴリ : %s\n" , $eachCategory );
            printf( "------------------------------------------------------------\n" );
            
            /** 分類辞書をメモリ上に読み込む。
              * 判定用スコアを計算するので、単語の最低出現回数は2回に引き下げる（第2引数に2を指定）
              */
            $bayes20ng->loadDictionary( $eachCategory , 2 );
            
            /* (1) $eachCategory に属するコーパスの判定用スコアの分布を調べる。 */
            
            /* $eachCategory に属するコーパスの判定用スコアの分布を記録する */
            $scoreDistTarget     = array();
            
            /* $eachCategory に含まれるコーパスファイルの一覧を取得する */
            $fileList       = $c20ng->getTrainingCorpusList( $eachCategory );
            
            /* $eachCategory に含まれるコーパスファイルの数 */
            $corpusSizeTarget   = sizeof( $fileList );
            
            /* 判定用スコアを計算する */
            foreach( $fileList as $eachFile )
            {
                $corpusBody             = $c20ng->getTrainingContent( $eachFile );
                $documentScore          = $bayes20ng->culcDocumentScore( $corpusBody );
                $scoreDistTarget[]      = $documentScore;
            }
            
            /* (2) $eachCategory 以外に属するコーパスの判定用スコアの分布を調べる */
            
            /* $eachCategory 以外に属するコーパスの判定用スコアの分布を記録する */
            $scoreDistExclude     = array();
            
            /* $eachCategory 以外に含まれるコーパスファイルの数 */
            $corpusSizeExclude    = 0;

            foreach( $categoryList as $eachCategoryExclude )
            {
                /* $eachCategory 以外のカテゴリの場合、処理を続行する */
                if( $eachCategory !== $eachCategoryExclude ){
                    /* $eachCategoryExclude に属するコーパスファイルの一覧 */
                    $fileListExclude    = $c20ng->getTrainingCorpusList( $eachCategoryExclude );
                    
                    $corpusSizeExclude += sizeof( $fileListExclude );
                    
                    /* 判定用スコアを計算する */
                    foreach( $fileListExclude as $eachFile )
                    {
                        $corpusBody             = $c20ng->getTrainingContent( $eachFile );
                        $documentScore          = $bayes20ng->culcDocumentScore( $corpusBody );
                        $scoreDistExclude[]     = $documentScore;
                    }
                }
            }
            
            /** 判定用スコアの分布を度数分布表にして表す。
              * 判定用スコアを0.1刻みの階級に分けて表示する。
              */
              
            /* $eachCategory に属するコーパスの判定用スコアの度数分布表 */
            $classDistTarget        = array();
            
            /* $eachCategory 以外に属するコーパスの判定用スコアの度数分布表 */
            $classDistExclude       = array();
            
            /* $eachCategory に属するコーパスの判定用スコアの度数分布表を作成する */
            foreach( $scoreDistTarget as $eachScore )
            {
                /* 小数点第一位までに桁を揃えて、キーにする */
                $eachKey            = sprintf( '%1.1f' , $eachScore );
                if( !isset( $classDistTarget[$eachKey] ) ){
                    $classDistTarget[$eachKey] = 1;
                }else{
                    $classDistTarget[$eachKey]++;
                }
            }
            
            /* $eachCategory 以外に属するコーパスの判定用スコアの度数分布表を作成する */
            foreach( $scoreDistExclude as $eachScore )
            {
                /* 小数点第一位までに桁を揃えて、キーにする */
                $eachKey            = sprintf( '%1.1f' , $eachScore );
                if( !isset( $classDistExclude[$eachKey] ) ){
                    $classDistExclude[$eachKey] = 1;
                }else{
                    $classDistExclude[$eachKey]++;
                }
            }
            
            /* 度数分布表を表示する */
            printf( "判定用スコアの分布\n" );
            printf( "判定用スコアの範囲  | %37s | %37s \n" ,
                    sprintf( '%s のコーパス' , $eachCategory ) ,
                    sprintf( '%s 以外のコーパス' , $eachCategory ) );
            for( $scoreRange = 0 ; $scoreRange <= 10 ; $scoreRange++ )
            {
                $eachKey    = sprintf( '%1.1f' , $scoreRange / 10 );
                
                /* 表示する判定用スコアの範囲 */
                $eachRange  = null;
                if( $scoreRange < 10 ){
                    $eachRange = sprintf( '%1.1f <= score <  %1.1f' , 
                                          $scoreRange / 10,
                                          $scoreRange / 10 + 0.1 );
                }else{
                    $eachRange = 'score = 1.0';
                }
                
                /* $eachCategory に属するコーパスの比率 */
                $eachDistTarget = 0;
                if( isset( $classDistTarget[$eachKey] ) ){
                    $eachDistTarget = $classDistTarget[$eachKey] / $corpusSizeTarget;
                }
                
                /* $eachCategory 以外に属するコーパスの比率 */
                $eachDistExclude    = 0;
                if( isset( $classDistExclude[$eachKey] ) ){
                    $eachDistExclude    = $classDistExclude[$eachKey] / $corpusSizeExclude;
                }
                printf( "%19s | %32s | %32s\n" ,
                        $eachRange ,
                        sprintf( '%1.3f ％' , $eachDistTarget * 100 ) ,
                        sprintf( '%1.3f ％' , $eachDistExclude * 100 ) );
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