<?php
/** サンプルプログラム : nlp_ai/training_code/code-5-5-3-j.php
  * [説明]
  * 「5.5.3.評価用データを使って性能を調べよう：けんてーごっこコーパス編」の内容を扱うサンプルプログラム。
  * 評価用データを使い、性能を評価する。
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
$bayesKentei    = new BayesLearning_Kentei();

/* MySQLに接続するためのパスワードを記入すること */
$bayesKentei->pdoPassword = '********';

/** 
  * @var    object      Corpus_Kentei クラスから生成したオブジェクト
  */
$ckentei        = new Corpus_Kentei();

if( is_object( $ckentei ) ){
    if( is_object( $bayesKentei ) ){
        printf( "***********************************************************\n" );
        printf( "ベイジアンフィルタ : 文書の判定用スコアを使った分類実験\n" );
        printf( "使用コーパス       : けんてーごっこ コーパス\n" );
        printf( "***********************************************************\n" );
        
        /* 各カテゴリの分類辞書をメモリ上に読み込み、判定用スコアの分布を計算する。 */
        $categoryList       = $ckentei->getTestCategoryList();
        
        foreach( $categoryList as $eachCategory )
        {
            printf( "------------------------------------------------------------\n" );
            printf( "●カテゴリ : %s\n" , $eachCategory );
            printf( "------------------------------------------------------------\n" );
            
            /** 分類辞書をメモリ上に読み込む。
              * 判定用スコアを計算するので、単語の最低出現回数は2回に引き下げる（第2引数に2を指定）
              */
            $bayesKentei->loadDictionary( $eachCategory , 2 );
            
            /* (1) $eachCategory に属するコーパスの判定用スコアの分布を調べる。 */
            
            /* $eachCategory に属するコーパスの判定用スコアの分布を記録する */
            $scoreDistTarget     = array();
            
            /* $eachCategory に含まれるコーパスファイルの一覧を取得する */
            $fileList           = $ckentei->getTestCorpusList( $eachCategory );
            
            /* $eachCategory に含まれるコーパスファイルの数 */
            $corpusSizeTarget   = sizeof( $fileList );
            
            /* 判定用スコアを計算する */
            foreach( $fileList as $eachFile )
            {
                $corpusBody             = $ckentei->getTestContent( $eachFile );
                $documentScore          = $bayesKentei->culcDocumentScore( $corpusBody );
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
                    $fileListExclude    = $ckentei->getTestCorpusList( $eachCategoryExclude );
                    
                    $corpusSizeExclude += sizeof( $fileListExclude );
                    
                    /* 判定用スコアを計算する */
                    foreach( $fileListExclude as $eachFile )
                    {
                        $corpusBody             = $ckentei->getTestContent( $eachFile );
                        $documentScore          = $bayesKentei->culcDocumentScore( $corpusBody );
                        $scoreDistExclude[]     = $documentScore;
                    }
                }
            }
            
            /* 判定用スコアの基準値を変えながら、最も分類性能が高い基準値を探す。 */
            
            /* 分類性能評価値の最高値を記録する */
            $bestSpecScore  = 0;
            
            /* 分類性能評価値の最高だった時の判定用スコアの基準値 */
            $bestScoreTh    = 0;
            
            /* 判定用スコアの基準値を 0.05 ずつ変えながら調べる */
            printf( "判定用スコア閾値 | 分類対象カテゴリに対する精度 | 分類対象カテゴリ以外に対する精度 | 分類精度評価値\n" );
            for( $scoreTh = 0.1 ; $scoreTh < 1.0 ; $scoreTh += 0.05 )
            {
                /* 分類対象カテゴリを正しく認識できた数 */
                $validCountTarget   = 0;
                
                /* 分類対象カテゴリ以外を正しく認識できた数 */
                $validCountExclude  = 0;
                
                /* 分類対象カテゴリを正しく認識できた数を調べる */
                foreach( $scoreDistTarget as $eachScore )
                {
                    /* 判定用スコアが基準値 $scoreTh 以上であれば、認識数を＋１する */
                    if( $eachScore >= $scoreTh ){
                        $validCountTarget++;
                    }
                }
                
                /* 分類対象カテゴリ以外を正しく認識できた数を調べる */
                foreach( $scoreDistExclude as $eachScore )
                {
                    /* 判定用スコアが基準値 $scoreTh 以下であれば、認識数を＋１する */
                    if( $eachScore <= $scoreTh ){
                        $validCountExclude++;
                    }
                }
                
                /* 分類対象カテゴリを正しく認識できた確率 */
                $precisionTarget    = $validCountTarget / $corpusSizeTarget;
                
                /* 分類対象カテゴリ以外を正しく認識できた確率 */
                $precisionExclude   = $validCountExclude / $corpusSizeExclude;
                
                /* 分類性能評価値 */
                $specScore    = $precisionTarget + $precisionExclude;
                
                printf( "%16s | %29s | %33s | %s\n" ,
                        sprintf( '%1.2f' , $scoreTh ) ,
                        sprintf( '%1.3f％' , $precisionTarget * 100 ) ,
                        sprintf( '%1.3f％' , $precisionExclude * 100 ) ,
                        sprintf( '%1.3f％' , $specScore * 100 ) );
                
                if( $specScore > $bestSpecScore ){
                    $bestSpecScore  = $specScore;
                    $bestScoreTh    = $scoreTh;
                }
            }
            printf( "\n" );
            printf( "分類性能が最も高かったのは、判定用スコアの基準値が %1.3f だったときです。\n" ,
                    $bestScoreTh );
            
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